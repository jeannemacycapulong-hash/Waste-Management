<?php
// update_pickup_status.php - Save collector pickup status to DB
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

if (getUserRole() !== 'collector') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$villager_id  = (int)($_POST['villager_id'] ?? 0);
$status       = $_POST['status'] ?? '';
$collector_id = getUserId();
$today        = date('Y-m-d');

$allowed = ['pending', 'completed', 'missed', 'no_waste'];
if (!$villager_id || !in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    $db = getDB();

    $stmt = $db->prepare("INSERT INTO collection_statuses
                            (villager_id, collector_id, collection_date, status)
                          VALUES (:vid, :cid, :date, :status)
                          ON DUPLICATE KEY UPDATE
                            status = :status2,
                            collector_id = :cid2,
                            updated_at = NOW()");

    $stmt->execute([
        ':vid'     => $villager_id,
        ':cid'     => $collector_id,
        ':date'    => $today,
        ':status'  => $status,
        ':status2' => $status,
        ':cid2'    => $collector_id
    ]);

    // Notify villager if missed
    if ($status === 'missed') {
        addNotification(
            $villager_id,
            'Collection Update',
            'Your garbage collection for today (' . date('F d, Y') . ') was marked as missed. Please ensure your bin is accessible next collection day.',
            'warning'
        );
    }

    echo json_encode(['success' => true, 'status' => $status]);

} catch (Exception $e) {
    error_log("update_pickup_status error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>