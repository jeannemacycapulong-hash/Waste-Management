<?php
// config.php - Configuration file
session_start();

// Hardcoded users for demo
$valid_users = [
    'user1' => 'pass1',
    'villager' => 'demo',
    'collector' => 'demo',
    'admin' => 'demo'
];

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
?>