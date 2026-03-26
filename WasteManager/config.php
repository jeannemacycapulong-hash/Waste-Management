<?php
// config.php - FULL CONFIGURATION FOR ALL ROLES
session_start();

// ================================
// DATABASE CONNECTION
// ================================
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $db = new PDO('mysql:host=db;dbname=wastemanager;charset=utf8', 'root', 'secret');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }
    return $db;
}

// ================================
// LOGIN & SESSION MANAGEMENT
// ================================
function loginUser($username, $password) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username AND is_active = 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Accept plain-text demo passwords or hashed passwords
        if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
            $_SESSION['user']      = $username;
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            // update last login
            $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $stmt->execute([':id' => $user['id']]);
            return true;
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
    }
    return false;
}

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

function getUserId() {
    return $_SESSION['user_id'] ?? 0;
}

function getUserName() {
    return $_SESSION['user_name'] ?? ($_SESSION['user'] ?? '');
}

// ================================
// NOTIFICATIONS
// ================================
function addNotification($userId, $title, $message, $type = 'info') {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:user_id,:title,:message,:type)");
        return $stmt->execute([
            ':user_id' => $userId,
            ':title'   => $title,
            ':message' => $message,
            ':type'    => $type
        ]);
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
    }
    return false;
}

function getUserNotifications($userId, $unreadOnly = true) {
    try {
        $db = getDB();
        $query = "SELECT * FROM notifications WHERE user_id = :user_id";
        if ($unreadOnly) $query .= " AND is_read = 0";
        $query .= " ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get notifications error: " . $e->getMessage());
    }
    return [];
}

// ================================
// VILLAGER FUNCTIONS
// ================================
function getVillagerTodayStatus($villagerId) {
    try {
        $db = getDB();
        $today = date('Y-m-d');
        $stmt = $db->prepare("
            SELECT c.status, u.name AS collector_name 
            FROM collection_statuses c
            LEFT JOIN users u ON c.collector_id = u.id
            WHERE c.villager_id = :vid AND c.collection_date = :today
            LIMIT 1
        ");
        $stmt->execute([':vid' => $villagerId, ':today' => $today]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get villager status error: " . $e->getMessage());
    }
    return null;
}

// ================================
// COLLECTOR FUNCTIONS
// ================================
function getCollectorSchedule($collectorId, $date = null) {
    try {
        $db = getDB();
        $date = $date ?? date('Y-m-d');
        $stmt = $db->prepare("
            SELECT c.id AS collection_id, v.name AS villager_name, c.status, c.collection_date
            FROM collection_statuses c
            JOIN users v ON c.villager_id = v.id
            WHERE c.collector_id = :cid AND c.collection_date = :date
        ");
        $stmt->execute([':cid' => $collectorId, ':date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get collector schedule error: " . $e->getMessage());
    }
    return [];
}

// ================================
// ADMIN FUNCTIONS
// ================================

// ✅ FIXED: Add missing function
function getCollectionStats() {
    try {
        $db = getDB();
        $today = date('Y-m-d');

        $stmt = $db->prepare("
            SELECT status, COUNT(*) as count
            FROM collection_statuses
            WHERE collection_date = :today
            GROUP BY status
        ");
        $stmt->execute([':today' => $today]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stats = [
            'total' => 0,
            'pending' => 0,
            'completed' => 0,
            'missed' => 0,
            'no_waste' => 0
        ];

        foreach ($results as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']] = $row['count'];
            }
            $stats['total'] += $row['count'];
        }

        return $stats;

    } catch (Exception $e) {
        error_log("Collection stats error: " . $e->getMessage());
    }

    return [
        'total' => 0,
        'pending' => 0,
        'completed' => 0,
        'missed' => 0,
        'no_waste' => 0
    ];
}

// ================================
// COLLECTION STATUS FUNCTIONS (ADMIN)
// ================================
function getTodayCollectionStatuses() {
    try {
        $db = getDB();
        $today = date('Y-m-d');

        $stmt = $db->prepare("
            SELECT c.*, 
                   v.name AS villager_name,
                   u.name AS collector_name
            FROM collection_statuses c
            LEFT JOIN users v ON c.villager_id = v.id
            LEFT JOIN users u ON c.collector_id = u.id
            WHERE c.collection_date = :today
        ");

        $stmt->execute([':today' => $today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Get today collection statuses error: " . $e->getMessage());
    }
    return [];
}

function getAllUsers() {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT id, username, name, role, is_active, created_at FROM users ORDER BY role, name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get all users error: " . $e->getMessage());
    }
    return [];
}

function deactivateUser($userId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE users SET is_active = 0 WHERE id = :id");
        return $stmt->execute([':id' => $userId]);
    } catch (Exception $e) {
        error_log("Deactivate user error: " . $e->getMessage());
    }
    return false;
}
// ================================
// REPORTS FUNCTIONS (ADMIN)
// ================================
function getAllReports() {
    try {
        $db = getDB();
        $stmt = $db->query("
            SELECT r.*, u.name AS reporter_name, u.role AS reporter_type
            FROM reports r
            LEFT JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get reports error: " . $e->getMessage());
    }
    return [];
}

// ================================
// DUES FUNCTIONS
// ================================
function getOrCreateCurrentDue($villagerId) {
    try {
        $db = getDB();
        $month = date('Y-m'); // current month in YYYY-MM format

        // Check if a due already exists for this villager and month
        $stmt = $db->prepare("SELECT * FROM monthly_dues WHERE villager_id = :vid AND DATE_FORMAT(due_date, '%Y-%m') = :month LIMIT 1");
        $stmt->execute([
            ':vid'   => $villagerId,
            ':month' => $month
        ]);
        $due = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($due) {
            return $due;
        } else {
            // Create a new due entry for the current month
            $stmt = $db->prepare("INSERT INTO monthly_dues (villager_id, amount, due_date, status, created_at, updated_at) VALUES (:vid, :amount, :due_date, 'pending', NOW(), NOW())");
            $stmt->execute([
                ':vid'      => $villagerId,
                ':amount'   => 100, // default due amount, adjust if needed
                ':due_date' => date('Y-m-01') // first day of the month
            ]);

            // Return the newly created due
            return [
                'id'          => $db->lastInsertId(),
                'villager_id' => $villagerId,
                'amount'      => 100,
                'due_date'    => date('Y-m-01'),
                'status'      => 'pending',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ];
        }

    } catch (Exception $e) {
        error_log("Get or create current due error: " . $e->getMessage());
        return null;
    }
}


function getVillagerDues($villagerId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM monthly_dues WHERE villager_id = :vid ORDER BY due_date DESC");
        $stmt->execute([':vid' => $villagerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get villager dues error: " . $e->getMessage());
        return [];
    }
}


// ================================
// LOGOUT
// ================================
function logout() {
    session_destroy();
}
?>