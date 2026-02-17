<?php
// placeholder.php - Placeholder for Collector/Admin roles
require_once 'config.php';
requireLogin();
$role = $_GET['role'] ?? 'user';
include 'header.php';
?>

<div class="placeholder-page">
    <i class="fas fa-tools fa-4x"></i>
    <h2><?php echo ucfirst($role); ?> Dashboard</h2>
    <p>This is a demo placeholder for the <?php echo $role; ?> interface.</p>
    <p>The full functionality would be implemented here.</p>
    <a href="role-selection.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back to Role Selection
    </a>
</div>

<?php include 'footer.php'; ?>