<?php
// send_notification.php - Handle admin broadcast notifications via AJAX
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

// Only admins can send notifications
if (getUserRole() !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$target  = $_POST['target']  ?? '';
$title   = $_POST['title']   ?? '';
$message = $_POST['message'] ?? '';
$type    = $_POST['type']    ?? 'info';
$specific_username = $_POST['specific_user'] ?? '';

if (empty($target) || empty($title) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (!in_array($type, ['info', 'success', 'warning'])) {
    $type = 'info';
}

try {
    $db = getDB();

    // Build query based on target
    if ($target === 'all') {
        $stmt = $db->query("SELECT id FROM users WHERE is_active = 1");
    } elseif ($target === 'villagers') {
        $stmt = $db->query("SELECT id FROM users WHERE role = 'villager' AND is_active = 1");
    } elseif ($target === 'collectors') {
        $stmt = $db->query("SELECT id FROM users WHERE role = 'collector' AND is_active = 1");
    } elseif ($target === 'specific') {
        if (empty($specific_username)) {
            echo json_encode(['success' => false, 'message' => 'No user selected']);
            exit;
        }
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username AND is_active = 1");
        $stmt->execute([':username' => $specific_username]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid target']);
        exit;
    }

    $recipients = $stmt->fetchAll();

    if (empty($recipients)) {
        echo json_encode(['success' => false, 'message' => 'No recipients found']);
        exit;
    }

    // Insert a notification for each recipient
    $insert = $db->prepare(
        "INSERT INTO notifications (user_id, title, message, type) VALUES (:user_id, :title, :message, :type)"
    );

    $count = 0;
    foreach ($recipients as $user) {
        $insert->execute([
            ':user_id' => $user['id'],
            ':title'   => $title,
            ':message' => $message,
            ':type'    => $type
        ]);
        $count++;
    }

    echo json_encode([
        'success' => true,
        'message' => "Notification sent to $count recipient(s) successfully."
    ]);

} catch (Exception $e) {
    error_log("send_notification.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
?>