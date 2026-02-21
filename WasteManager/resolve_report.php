<?php
// resolve_report.php - Handle report resolution via AJAX
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

// Check if user is admin
if (getUserRole() !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['resolve_report'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get form data
$reportId = $_POST['report_id'] ?? '';
$response = $_POST['response'] ?? '';
$notifyAll = isset($_POST['notify_all']) && $_POST['notify_all'] === '1';

// Log for debugging
error_log("resolve_report.php - Report ID: $reportId, Response: $response, Notify All: " . ($notifyAll ? 'Yes' : 'No'));

// Validate
if (empty($reportId) || empty($response)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Resolve the report
$result = resolveReport($reportId, $response, $notifyAll);

if ($result) {
    // Get report details to know what role was affected (for response message)
    try {
        $db = getDB();
        $query = "SELECT reporter_type FROM reports WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $reportId]);
        $report = $stmt->fetch();
        $role = $report ? $report['reporter_type'] : 'user';
    } catch (Exception $e) {
        error_log("resolve_report.php - Error fetching report type: " . $e->getMessage());
        $role = 'user';
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Report resolved successfully',
        'role' => $role
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to resolve report. It may already be resolved.']);
}
?>