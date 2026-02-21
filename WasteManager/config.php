<?php
// config.php - Configuration file (SIMPLIFIED VERSION)
session_start();

// Include database connection
require_once 'database.php';

// Hardcoded users for demo with roles and names (KEEP THIS FOR backward compatibility)
$valid_users = [
    'user1' => ['password' => 'pass1', 'role' => 'villager', 'name' => 'Juan Dela Cruz'],
    'villager' => ['password' => 'demo', 'role' => 'villager', 'name' => 'Maria Santos'],
    'collector' => ['password' => 'demo', 'role' => 'collector', 'name' => 'Pedro Reyes'],
    'admin' => ['password' => 'demo', 'role' => 'admin', 'name' => 'Admin User']
];

// Initialize session arrays for backward compatibility
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}

if (!isset($_SESSION['reports'])) {
    $_SESSION['reports'] = [
        'villager' => [],
        'collector' => []
    ];
}

// Keep this for backward compatibility with collector dashboard
if (!isset($_SESSION['pickup_statuses'])) {
    $_SESSION['pickup_statuses'] = [
        ['id' => 1, 'villager' => 'Juan Dela Cruz', 'address' => 'Blk 1 Lot 2, Pampang Purok', 'status' => 'pending', 'date' => '2026-02-19', 'collector' => 'collector'],
        ['id' => 2, 'villager' => 'Maria Santos', 'address' => 'Blk 2 Lot 5, Pampang Purok', 'status' => 'pending', 'date' => '2026-02-19', 'collector' => 'collector'],
        ['id' => 3, 'villager' => 'Pedro Reyes', 'address' => 'Blk 3 Lot 8, Pampang Purok', 'status' => 'completed', 'date' => '2026-02-19', 'collector' => 'collector'],
        ['id' => 4, 'villager' => 'Ana Lopez', 'address' => 'Blk 4 Lot 12, Pampang Purok', 'status' => 'missed', 'date' => '2026-02-19', 'collector' => 'collector'],
        ['id' => 5, 'villager' => 'Jose Mercado', 'address' => 'Blk 5 Lot 3, Pampang Purok', 'status' => 'no_waste', 'date' => '2026-02-19', 'collector' => 'collector']
    ];
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
        
        // Check against database first
        if ($user && $user['password'] === $password) {
            $_SESSION['user'] = $username;
            $_SESSION['user_id'] = $user['id'];
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
    
    // Fallback to old array for backward compatibility during migration
    if (isset($GLOBALS['valid_users'][$username]) && 
        $GLOBALS['valid_users'][$username]['password'] === $password) {
        $_SESSION['user'] = $username;
        $_SESSION['user_role'] = $GLOBALS['valid_users'][$username]['role'];
        $_SESSION['user_name'] = $GLOBALS['valid_users'][$username]['name'];
        return true;
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
    if (isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'];
    }
    $username = $_SESSION['user'] ?? '';
    return $GLOBALS['valid_users'][$username]['role'] ?? 'villager';
}

function getUserName() {
    if (isset($_SESSION['user_name'])) {
        return $_SESSION['user_name'];
    }
    $username = $_SESSION['user'] ?? '';
    return $GLOBALS['valid_users'][$username]['name'] ?? $username;
}

function getUserId() {
    return $_SESSION['user_id'] ?? 0;
}

function getUserDisplayName($username) {
    try {
        $db = getDB();
        $query = "SELECT name FROM users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        if ($user) {
            return $user['name'];
        }
    } catch (Exception $e) {
        error_log("Database error in getUserDisplayName: " . $e->getMessage());
    }
    return $GLOBALS['valid_users'][$username]['name'] ?? $username;
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
    return $GLOBALS['valid_users'][$username] ?? false;
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
                                       description, contact_number, urgency) 
                  VALUES (:reporter_id, :reporter_type, :issue_type, :location, 
                          :description, :contact, :urgency)";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':reporter_id' => $userId,
            ':reporter_type' => $type,
            ':issue_type' => $data['issue_type'] ?? 'other',
            ':location' => $data['location'] ?? '',
            ':description' => $data['description'] ?? '',
            ':contact' => $data['contact'] ?? null,
            ':urgency' => $data['urgency'] ?? 'low'
        ]);
        
        if ($result) {
            return $db->lastInsertId();
        }
    } catch (Exception $e) {
        error_log("Database add report failed: " . $e->getMessage());
    }
    
    // Fallback to session
    $report = [
        'id' => uniqid(),
        'type' => $type,
        'reporter' => $_SESSION['user'],
        'reporter_name' => getUserName(),
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
        'resolved_at' => null,
        'admin_response' => null
    ];
    // Merge with data
    foreach ($data as $key => $value) {
        $report[$key] = $value;
    }
    $_SESSION['reports'][$type][] = $report;
    return $report;
}

function getAllReports() {
    try {
        $db = getDB();
        $query = "SELECT r.*, u.name as reporter_name, u.username 
                  FROM reports r
                  JOIN users u ON r.reporter_id = u.id
                  ORDER BY r.created_at DESC";
        $stmt = $db->query($query);
        $results = $stmt->fetchAll();
        
        error_log("getAllReports found " . count($results) . " reports from database");
        return $results;
        
    } catch (Exception $e) {
        error_log("Database get all reports failed: " . $e->getMessage());
    }
    
    // Fallback to session data
    $all = [];
    if (isset($_SESSION['reports'])) {
        foreach ($_SESSION['reports'] as $type => $reports) {
            foreach ($reports as $report) {
                if (!isset($report['reporter_type']) && isset($report['type'])) {
                    $report['reporter_type'] = $report['type'];
                }
                if (!isset($report['reporter_name']) && isset($report['reporter'])) {
                    $report['reporter_name'] = $GLOBALS['valid_users'][$report['reporter']]['name'] ?? $report['reporter'];
                }
                if (!isset($report['username']) && isset($report['reporter'])) {
                    $report['username'] = $report['reporter'];
                }
                if (!isset($report['issue_type'])) {
                    $report['issue_type'] = $report['type'] ?? 'general';
                }
                if (!isset($report['location'])) {
                    $report['location'] = $report['address'] ?? 'N/A';
                }
                if (!isset($report['description'])) {
                    $report['description'] = $report['message'] ?? 'No description';
                }
                if (!isset($report['urgency'])) {
                    $report['urgency'] = 'low';
                }
                $all[] = $report;
            }
        }
    }
    
    usort($all, function($a, $b) {
        $date_a = $a['created_at'] ?? '1970-01-01';
        $date_b = $b['created_at'] ?? '1970-01-01';
        return strtotime($date_b) - strtotime($date_a);
    });
    
    return $all;
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
        error_log("Database get reports by type failed: " . $e->getMessage());
    }
    return $_SESSION['reports'][$type] ?? [];
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
        error_log("Database get report by ID failed: " . $e->getMessage());
    }
    
    foreach ($_SESSION['reports'] as $type => $reports) {
        foreach ($reports as $report) {
            if ($report['id'] === $reportId) {
                return $report;
            }
        }
    }
    return null;
}

function resolveReport($reportId, $adminResponse, $notifyAll = false) {
    try {
        $db = getDB();
        $adminId = getUserId();
        
        // Get report details
        $getReportQuery = "SELECT r.*, u.name as reporter_name, u.username, u.role 
                          FROM reports r
                          JOIN users u ON r.reporter_id = u.id
                          WHERE r.id = :id";
        $getReportStmt = $db->prepare($getReportQuery);
        $getReportStmt->execute([':id' => $reportId]);
        $report = $getReportStmt->fetch();
        
        if (!$report) {
            error_log("resolveReport failed: Report ID $reportId not found");
            return false;
        }
        
        // Update the report status
        $query = "UPDATE reports SET 
                  status = 'resolved', 
                  admin_response = :response,
                  resolved_by = :admin_id, 
                  resolved_at = NOW()
                  WHERE id = :id AND status = 'pending'";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':response' => $adminResponse,
            ':admin_id' => $adminId,
            ':id' => $reportId
        ]);
        
        if ($result && $stmt->rowCount() > 0) {
            // Notify the original reporter
            addNotification(
                $report['reporter_id'],
                'Your Issue Has Been Resolved',
                "Your report has been reviewed and resolved. Admin response: " . $adminResponse,
                'success'
            );
            
            // Notify all admins (except the one who resolved it)
            $adminQuery = "SELECT id FROM users WHERE role = 'admin' AND id != :admin_id AND is_active = 1";
            $adminStmt = $db->prepare($adminQuery);
            $adminStmt->execute([':admin_id' => $adminId]);
            
            while ($admin = $adminStmt->fetch()) {
                addNotification(
                    $admin['id'],
                    'Report Resolved',
                    "Report #$reportId from " . $report['reporter_name'] . " has been resolved.",
                    'info'
                );
            }
            
            error_log("resolveReport: Successfully resolved report ID: $reportId");
            return true;
        } else {
            error_log("resolveReport: No rows updated for report ID: $reportId (maybe already resolved?)");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Database resolve report failed: " . $e->getMessage());
    }
    
    // Fallback to session method
    if (isset($_SESSION['reports'])) {
        foreach ($_SESSION['reports'] as $type => &$reports) {
            foreach ($reports as &$report) {
                if ($report['id'] === $reportId) {
                    $report['status'] = 'resolved';
                    $report['resolved_at'] = date('Y-m-d H:i:s');
                    $report['admin_response'] = $adminResponse;
                    
                    if (isset($report['reporter'])) {
                        addNotification(
                            $report['reporter'],
                            'Your Issue Has Been Resolved',
                            $adminResponse,
                            'success'
                        );
                    }
                    return true;
                }
            }
        }
    }
    
    return false;
}

// ============================================
// SIMPLIFIED PICKUP FUNCTIONS (using session only)
// ============================================

function getPickupStats() {
    $stats = [
        'total' => count($_SESSION['pickup_statuses']),
        'pending' => 0,
        'completed' => 0,
        'missed' => 0,
        'no_waste' => 0
    ];
    
    foreach ($_SESSION['pickup_statuses'] as $pickup) {
        $stats[$pickup['status']]++;
    }
    
    return $stats;
}

function getTodaysPickups($collectorId = null) {
    return $_SESSION['pickup_statuses'];
}

function updatePickupStatus($pickupId, $newStatus, $notes = null) {
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

function getVillagerDues($villagerId) {
    return [];
}

function getCurrentDue($villagerId) {
    return null;
}

function payMonthlyDue($dueId, $paymentMethod = 'cash', $reference = null) {
    return false;
}

// ============================================
// USER MANAGEMENT FUNCTIONS
// ============================================

function getAllUsersByRole($role = null) {
    try {
        $db = getDB();
        
        $query = "SELECT id, username, name, role, contact_number, address, created_at, last_login 
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
        error_log("Database get all users failed: " . $e->getMessage());
    }
    
    // Fallback to valid_users array
    $users = [];
    foreach ($GLOBALS['valid_users'] as $username => $data) {
        if (!$role || $data['role'] === $role) {
            $users[] = [
                'username' => $username,
                'name' => $data['name'],
                'role' => $data['role']
            ];
        }
    }
    return $users;
}

// ============================================
// LOGOUT FUNCTION
// ============================================

function logout() {
    session_destroy();
}
?>