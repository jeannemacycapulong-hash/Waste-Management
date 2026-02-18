<?php
// config.php - Configuration file
session_start();

// Hardcoded users for demo with roles and names
$valid_users = [
    'user1' => ['password' => 'pass1', 'role' => 'villager', 'name' => 'Juan Dela Cruz'],
    'villager' => ['password' => 'demo', 'role' => 'villager', 'name' => 'Maria Santos'],
    'collector' => ['password' => 'demo', 'role' => 'collector', 'name' => 'Pedro Reyes'],
    'admin' => ['password' => 'demo', 'role' => 'admin', 'name' => 'Admin User']
];

// Initialize notifications array in session if not exists
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}

// Initialize reports array in session if not exists
if (!isset($_SESSION['reports'])) {
    $_SESSION['reports'] = [
        'villager' => [],
        'collector' => []
    ];
}

// Initialize pickup statuses (linked to collector dashboard)
if (!isset($_SESSION['pickup_statuses'])) {
    $_SESSION['pickup_statuses'] = [
        ['id' => 1, 'villager' => 'Juan Dela Cruz', 'address' => 'Blk 1 Lot 2, Pampang Purok', 'status' => 'pending', 'date' => '2026-02-19', 'collector' => 'collector'],
        ['id' => 2, 'villager' => 'Maria Santos', 'address' => 'Blk 2 Lot 5, Pampang Purok', 'status' => 'pending', 'date' => '2026-02-19', 'collector' => 'collector'],
        ['id' => 3, 'villager' => 'Pedro Reyes', 'address' => 'Blk 3 Lot 8, Pampang Purok', 'status' => 'completed', 'date' => '2026-02-19', 'collector' => 'collector'],
        ['id' => 4, 'villager' => 'Ana Lopez', 'address' => 'Blk 4 Lot 12, Pampang Purok', 'status' => 'missed', 'date' => '2026-02-19', 'collector' => 'collector'],
        ['id' => 5, 'villager' => 'Jose Mercado', 'address' => 'Blk 5 Lot 3, Pampang Purok', 'status' => 'no_waste', 'date' => '2026-02-19', 'collector' => 'collector']
    ];
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

// Function to get user role
function getUserRole() {
    $username = $_SESSION['user'] ?? '';
    return $GLOBALS['valid_users'][$username]['role'] ?? 'villager';
}

// Function to get user name
function getUserName() {
    $username = $_SESSION['user'] ?? '';
    return $GLOBALS['valid_users'][$username]['name'] ?? $username;
}

// Function to get user display name for notifications
function getUserDisplayName($username) {
    return $GLOBALS['valid_users'][$username]['name'] ?? $username;
}

// Function to add notification
function addNotification($userId, $title, $message, $type = 'info') {
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

// Function to get user notifications
function getUserNotifications($userId) {
    return array_filter($_SESSION['notifications'], function($n) use ($userId) {
        return $n['user_id'] === $userId && !$n['read'];
    });
}

// Function to get all notifications for a user (including read)
function getAllUserNotifications($userId) {
    return array_filter($_SESSION['notifications'], function($n) use ($userId) {
        return $n['user_id'] === $userId;
    });
}

// Function to mark notification as read
function markNotificationRead($notificationId) {
    foreach ($_SESSION['notifications'] as &$notification) {
        if ($notification['id'] === $notificationId) {
            $notification['read'] = true;
            break;
        }
    }
}

// Function to mark all notifications as read for a user
function markAllNotificationsRead($userId) {
    foreach ($_SESSION['notifications'] as &$notification) {
        if ($notification['user_id'] === $userId) {
            $notification['read'] = true;
        }
    }
}

// Function to add report
function addReport($type, $data) {
    $report = [
        'id' => uniqid(),
        'type' => $type,
        'reporter' => $_SESSION['user'],
        'reporter_name' => getUserName(),
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
        'resolved_at' => null,
        'admin_response' => null,
        ...$data
    ];
    $_SESSION['reports'][$type][] = $report;
    
    // Add notification for admin
    addNotification(
        'admin',
        'New ' . ucfirst($type) . ' Report',
        getUserName() . ' reported a new issue: ' . ($data['issue_type'] ?? 'General'),
        'info'
    );
    
    return $report;
}

// Function to get all reports
function getAllReports() {
    $all = [];
    foreach ($_SESSION['reports'] as $type => $reports) {
        foreach ($reports as $report) {
            $all[] = $report;
        }
    }
    // Sort by date, newest first
    usort($all, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    return $all;
}

// Function to get reports by type
function getReportsByType($type) {
    return $_SESSION['reports'][$type] ?? [];
}

// Function to get report by ID
function getReportById($reportId) {
    foreach ($_SESSION['reports'] as $type => $reports) {
        foreach ($reports as $report) {
            if ($report['id'] === $reportId) {
                return $report;
            }
        }
    }
    return null;
}

// Function to resolve report
function resolveReport($reportId, $adminResponse, $notifyAll = false) {
    foreach ($_SESSION['reports'] as $type => &$reports) {
        foreach ($reports as &$report) {
            if ($report['id'] === $reportId) {
                $report['status'] = 'resolved';
                $report['resolved_at'] = date('Y-m-d H:i:s');
                $report['admin_response'] = $adminResponse;
                
                // Send notification to the reporter
                addNotification(
                    $report['reporter'],
                    'Your Issue Has Been Resolved',
                    $adminResponse,
                    'success'
                );
                
                // If notifyAll is true, send to all villagers/collectors
                if ($notifyAll) {
                    $targetRole = ($type === 'villager') ? 'villager' : 'collector';
                    foreach ($GLOBALS['valid_users'] as $username => $userData) {
                        if ($userData['role'] === $targetRole && $username !== $report['reporter']) {
                            addNotification(
                                $username,
                                'Issue Resolution Update',
                                'An issue has been resolved: ' . substr($adminResponse, 0, 50) . '...',
                                'info'
                            );
                        }
                    }
                }
                return true;
            }
        }
    }
    return false;
}

// Function to update pickup status (called from collector dashboard)
function updatePickupStatus($pickupId, $newStatus) {
    foreach ($_SESSION['pickup_statuses'] as &$pickup) {
        if ($pickup['id'] == $pickupId) {
            $pickup['status'] = $newStatus;
            $pickup['updated_at'] = date('Y-m-d H:i:s');
            
            // Notify admin of status change
            addNotification(
                'admin',
                'Pickup Status Updated',
                'Pickup for ' . $pickup['villager'] . ' is now ' . str_replace('_', ' ', $newStatus),
                'info'
            );
            return true;
        }
    }
    return false;
}

// Function to get pickup statistics
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

// Function to send broadcast notification
function sendBroadcastNotification($target, $title, $message, $type = 'info') {
    $count = 0;
    foreach ($GLOBALS['valid_users'] as $username => $userData) {
        if ($target === 'all' || 
            ($target === 'villagers' && $userData['role'] === 'villager') ||
            ($target === 'collectors' && $userData['role'] === 'collector') ||
            ($target === 'specific' && $username === $specificUser)) {
            addNotification($username, $title, $message, $type);
            $count++;
        }
    }
    return $count;
}
?>