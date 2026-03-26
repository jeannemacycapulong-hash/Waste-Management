<?php
// submit_report.php - Handle report submissions via AJAX
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit_report'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get form data
$reporter_type = $_POST['reporter_type'] ?? '';
$issue_type = $_POST['issue_type'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';
$contact = $_POST['contact'] ?? '';
$urgency = $_POST['urgency'] ?? 'low';

// Log for debugging
error_log("Report submission - Type: $reporter_type, Issue: $issue_type, Location: $location");

// Validate
if (empty($issue_type) || empty($location) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Add the report
$result = addReport($reporter_type, [
    'issue_type' => $issue_type,
    'location' => $location,
    'description' => $description,
    'contact' => $contact,
    'urgency' => $urgency
]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit report']);
}
?>