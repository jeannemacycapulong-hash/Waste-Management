<?php
// config.php - Configuration file (SIMPLIFIED VERSION)
session_start();

// Include database connection
require_once 'database.php';

// Session initialization
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}

// ============================================
// DATABASE LOGIN FUNCTION
// ============================================
function loginUser($username, $password) {
    try {
        $db = getDB();

        $query = "SELECT * FROM users WHERE username = :username AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && $user['password'] === $password) {
            $_SESSION['user']      = $username;
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            // Update last login
            $update = "UPDATE users SET last_login = NOW() WHERE id = :id";
            $stmt = $db->prepare($update);
            $stmt->execute([':id' => $user['id']]);

            return true;
        }
    } catch (Exception $e) {
        error_log("Database login error: " . $e->getMessage());
    }

    return false;
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function getUserRole() {
    return $_SESSION['user_role'] ?? 'villager';
}

function getUserName() {
    return $_SESSION['user_name'] ?? ($_SESSION['user'] ?? '');
}

function getUserId() {
    return $_SESSION['user_id'] ?? 0;
}

function getUserDisplayName($username) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT name FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        return $user ? $user['name'] : $username;
    } catch (Exception $e) {
        error_log("getUserDisplayName error: " . $e->getMessage());
        return $username;
    }
}

function getUserByUsername($username) {
    try {
        $db = getDB();
        $query = "SELECT * FROM users WHERE username = :username AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Database get user failed: " . $e->getMessage());
    }
    return false;
}

function getUserById($userId) {
    try {
        $db = getDB();
        $query = "SELECT * FROM users WHERE id = :id AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Database get user by ID failed: " . $e->getMessage());
    }
    return false;
}

// ============================================
// NOTIFICATION FUNCTIONS
// ============================================

function addNotification($userId, $title, $message, $type = 'info') {
    try {
        $db = getDB();
        
        $query = "INSERT INTO notifications (user_id, title, message, type) 
                  VALUES (:user_id, :title, :message, :type)";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':message' => $message,
            ':type' => $type
        ]);
        
        if ($result) {
            return true;
        }
    } catch (Exception $e) {
        error_log("Database notification failed: " . $e->getMessage());
    }
    
    // Fallback to old session method
    $notification = [
        'id' => uniqid(),
        'user_id' => $userId,
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'read' => false,
        'created_at' => date('Y-m-d H:i:s')
    ];
    $_SESSION['notifications'][] = $notification;
    return $notification;
}

function getUserNotifications($userId, $unreadOnly = true) {
    try {
        $db = getDB();
        
        $query = "SELECT * FROM notifications WHERE user_id = :user_id";
        if ($unreadOnly) {
            $query .= " AND is_read = 0";
        }
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        $results = $stmt->fetchAll();
        
        // Convert database format to match what the app expects
        foreach ($results as &$notification) {
            $notification['read'] = $notification['is_read'];
        }
        
        return $results;
    } catch (Exception $e) {
        error_log("Database get notifications failed: " . $e->getMessage());
    }
    
    // Fallback to session
    return array_filter($_SESSION['notifications'], function($n) use ($userId) {
        return $n['user_id'] == $userId && (!$unreadOnly || !$n['read']);
    });
}

function getAllUserNotifications($userId) {
    try {
        $db = getDB();
        
        $query = "SELECT * FROM notifications 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        $results = $stmt->fetchAll();
        
        // Convert database format to match what the app expects
        foreach ($results as &$notification) {
            $notification['read'] = $notification['is_read'];
        }
        
        return $results;
        
    } catch (Exception $e) {
        error_log("Database get all notifications failed: " . $e->getMessage());
    }
    
    // Fallback to session
    return array_filter($_SESSION['notifications'] ?? [], function($n) use ($userId) {
        return $n['user_id'] == $userId;
    });
}

function getUnreadNotificationCount($userId) {
    try {
        $db = getDB();
        
        $query = "SELECT COUNT(*) as count FROM notifications 
                  WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $db->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
        
    } catch (Exception $e) {
        error_log("Database get notification count failed: " . $e->getMessage());
    }
    
    // Fallback to session
    return count(array_filter($_SESSION['notifications'] ?? [], function($n) use ($userId) {
        return $n['user_id'] == $userId && !$n['read'];
    }));
}

function markNotificationRead($notificationId) {
    try {
        $db = getDB();
        $query = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($query);
        return $stmt->execute([':id' => $notificationId]);
    } catch (Exception $e) {
        error_log("Database mark read failed: " . $e->getMessage());
    }
    
    // Fallback to session
    foreach ($_SESSION['notifications'] as &$notification) {
        if ($notification['id'] === $notificationId) {
            $notification['read'] = true;
            break;
        }
    }
    return true;
}

function markAllNotificationsRead($userId) {
    try {
        $db = getDB();
        $query = "UPDATE notifications SET is_read = 1, read_at = NOW() 
                  WHERE user_id = :user_id AND is_read = 0";
        $stmt = $db->prepare($query);
        return $stmt->execute([':user_id' => $userId]);
    } catch (Exception $e) {
        error_log("Database mark all read failed: " . $e->getMessage());
    }
    
    // Fallback to session
    foreach ($_SESSION['notifications'] as &$notification) {
        if ($notification['user_id'] == $userId) {
            $notification['read'] = true;
        }
    }
    return true;
}

// ============================================
// REPORT FUNCTIONS
// ============================================

function addReport($type, $data) {
    try {
        $db = getDB();
        $userId = getUserId();

        $query = "INSERT INTO reports (reporter_id, reporter_type, issue_type, location,
                                       description, urgency)
                  VALUES (:reporter_id, :reporter_type, :issue_type, :location,
                          :description, :urgency)";

        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':reporter_id'   => $userId,
            ':reporter_type' => $type,
            ':issue_type'    => $data['issue_type'] ?? 'other',
            ':location'      => $data['location'] ?? '',
            ':description'   => $data['description'] ?? '',
            ':urgency'       => $data['urgency'] ?? 'low'
        ]);

        return $result ? $db->lastInsertId() : false;
    } catch (Exception $e) {
        error_log("addReport error: " . $e->getMessage());
        return false;
    }
}

function getAllReports() {
    try {
        $db = getDB();
        $query = "SELECT r.*, u.name as reporter_name, u.username
                  FROM reports r
                  JOIN users u ON r.reporter_id = u.id
                  ORDER BY r.created_at DESC";
        $stmt = $db->query($query);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("getAllReports error: " . $e->getMessage());
        return [];
    }
}

function getReportsByType($type) {
    try {
        $db = getDB();
        $query = "SELECT r.*, u.name as reporter_name, u.username 
                  FROM reports r
                  JOIN users u ON r.reporter_id = u.id
                  WHERE r.reporter_type = :type
                  ORDER BY r.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("getReportsByType error: " . $e->getMessage());
        return [];
    }
}

function getReportById($reportId) {
    try {
        $db = getDB();
        $query = "SELECT r.*, u.name as reporter_name, u.username
                  FROM reports r
                  JOIN users u ON r.reporter_id = u.id
                  WHERE r.id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $reportId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("getReportById error: " . $e->getMessage());
        return null;
    }
}

function resolveReport($reportId, $adminResponse, $notifyAll = false) {
    try {
        $db = getDB();
        $adminId = getUserId();

        // Get report details
        $getReportStmt = $db->prepare("SELECT r.*, u.name as reporter_name, u.username, u.role
                                       FROM reports r
                                       JOIN users u ON r.reporter_id = u.id
                                       WHERE r.id = :id");
        $getReportStmt->execute([':id' => $reportId]);
        $report = $getReportStmt->fetch();

        if (!$report) {
            error_log("resolveReport: Report ID $reportId not found");
            return false;
        }

        $stmt = $db->prepare("UPDATE reports SET
                              status = 'resolved',
                              admin_response = :response,
                              resolved_by = :admin_id,
                              resolved_at = NOW()
                              WHERE id = :id AND status = 'pending'");

        $result = $stmt->execute([
            ':response' => $adminResponse,
            ':admin_id' => $adminId,
            ':id'       => $reportId
        ]);

        if ($result && $stmt->rowCount() > 0) {
            addNotification(
                $report['reporter_id'],
                'Your Issue Has Been Resolved',
                "Your report has been reviewed and resolved. Admin response: " . $adminResponse,
                'success'
            );

            $adminStmt = $db->prepare("SELECT id FROM users WHERE role = 'admin' AND id != :admin_id AND is_active = 1");
            $adminStmt->execute([':admin_id' => $adminId]);
            while ($admin = $adminStmt->fetch()) {
                addNotification($admin['id'], 'Report Resolved', "Report #$reportId from " . $report['reporter_name'] . " has been resolved.", 'info');
            }

            return true;
        }

        return false;

    } catch (Exception $e) {
        error_log("resolveReport error: " . $e->getMessage());
        return false;
    }
}

// ============================================
// PICKUP FUNCTIONS (session-based, future DB upgrade)
// ============================================

function getPickupStats() {
    try {
        $db = getDB();
        $total = $db->query("SELECT COUNT(*) FROM users WHERE role = 'villager' AND is_active = 1")->fetchColumn();
        return [
            'total'     => (int)$total,
            'pending'   => (int)$total,
            'completed' => 0,
            'missed'    => 0,
            'no_waste'  => 0
        ];
    } catch (Exception $e) {
        error_log("getPickupStats error: " . $e->getMessage());
        return ['total' => 0, 'pending' => 0, 'completed' => 0, 'missed' => 0, 'no_waste' => 0];
    }
}

function getTodaysPickups($collectorId = null) {
    return $_SESSION['pickup_statuses'] ?? [];
}

function updatePickupStatus($pickupId, $newStatus, $notes = null) {
    if (!isset($_SESSION['pickup_statuses'])) return false;
    foreach ($_SESSION['pickup_statuses'] as &$pickup) {
        if ($pickup['id'] == $pickupId) {
            $pickup['status'] = $newStatus;
            $pickup['updated_at'] = date('Y-m-d H:i:s');
            return true;
        }
    }
    return false;
}

// ============================================
// SIMPLIFIED COLLECTION SCHEDULE (hardcoded)
// ============================================

function getCollectionSchedules() {
    return [
        ['collection_day' => 'Monday', 'collection_time' => '08:30:00', 'location_area' => 'Baranggay Pampang Purok, Angeles City', 'waste_types' => 'dry,wet'],
        ['collection_day' => 'Thursday', 'collection_time' => '08:30:00', 'location_area' => 'Baranggay Pampang Purok, Angeles City', 'waste_types' => 'dry,wet']
    ];
}

function getUpcomingCollections($limit = 5) {
    return [];
}

// ============================================
// SIMPLIFIED MONTHLY DUES (using session fallback)
// ============================================

function getOrCreateCurrentDue($villagerId) {
    try {
        $db = getDB();
        $thisMonth = date('Y-m-01');

        // Check if a record already exists for this month
        $stmt = $db->prepare("SELECT * FROM monthly_dues WHERE villager_id = :vid AND due_month = :month");
        $stmt->execute([':vid' => $villagerId, ':month' => $thisMonth]);
        $due = $stmt->fetch();

        if (!$due) {
            // Auto-create this month's due
            $stmt = $db->prepare("INSERT INTO monthly_dues (villager_id, due_month, amount, status)
                                  VALUES (:vid, :month, 1000.00, 'unpaid')");
            $stmt->execute([':vid' => $villagerId, ':month' => $thisMonth]);
            $newId = $db->lastInsertId();

            $stmt = $db->prepare("SELECT * FROM monthly_dues WHERE id = :id");
            $stmt->execute([':id' => $newId]);
            $due = $stmt->fetch();
        }

        return $due;
    } catch (Exception $e) {
        error_log("getOrCreateCurrentDue error: " . $e->getMessage());
        return null;
    }
}

function getVillagerDues($villagerId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM monthly_dues WHERE villager_id = :vid ORDER BY due_month DESC");
        $stmt->execute([':vid' => $villagerId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("getVillagerDues error: " . $e->getMessage());
        return [];
    }
}

function getCurrentDue($villagerId) {
    return getOrCreateCurrentDue($villagerId);
}

function payMonthlyDue($dueId, $paymentMethod = 'cash') {
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE monthly_dues
                              SET status = 'paid', payment_method = :method, paid_at = NOW()
                              WHERE id = :id AND status = 'unpaid'");
        $stmt->execute([':method' => $paymentMethod, ':id' => $dueId]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("payMonthlyDue error: " . $e->getMessage());
        return false;
    }
}

// ============================================
// USER MANAGEMENT FUNCTIONS
// ============================================

function getAllUsersByRole($role = null) {
    try {
        $db = getDB();

        $query = "SELECT id, username, name, role, email, contact_number, address, created_at, last_login
                  FROM users WHERE is_active = 1";

        if ($role) {
            $query .= " AND role = :role";
        }

        $query .= " ORDER BY name";

        $stmt = $db->prepare($query);
        if ($role) {
            $stmt->execute([':role' => $role]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("getAllUsersByRole error: " . $e->getMessage());
        return [];
    }
}

// ============================================
// LOGOUT FUNCTION
// ============================================

function logout() {
    session_destroy();
}
?>