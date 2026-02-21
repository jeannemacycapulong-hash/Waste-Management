<?php
// role-selection.php - Role Selection after login
require_once 'config.php';
requireLogin();
include 'header.php';
?>

<div class="role-selection">
    <h2>Select User Role</h2>
    <p class="welcome-user">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>! 
        Choose your interface.
    </p>
    
    <div class="role-cards">
        <!-- Villager Card -->
        <div class="role-card villager">
            <i class="fas fa-home role-icon"></i>
            <h3>Villager</h3>
            <ul class="role-options">
                <li><i class="fas fa-calendar-alt"></i> View Garbage Collection Schedule</li>
                <li><i class="fas fa-route"></i> View daily collection routes</li>
                <li><i class="fas fa-clipboard-check"></i> Monitor Village Cleanliness</li>
                <li><i class="fas fa-check-circle"></i> Confirmation Of Garbage Collect</li>
            </ul>
            <a href="villager-dashboard.php" class="role-link">
                GO TO DASHBOARD →
            </a>
        </div>

        <!-- Collector Card -->
        <div class="role-card collector">
            <i class="fas fa-truck role-icon"></i>
            <h3>Collector</h3>
            <ul class="role-options">
                <li><i class="fas fa-dumpster"></i> Collect garbage from villages</li>
                <li><i class="fas fa-sync-alt"></i> Update collection status</li>
                <li><i class="fas fa-clipboard-check"></i> Track pickups</li>
                <li><i class="fas fa-exclamation-triangle"></i> Report issues</li>
            </ul>
            <!-- CHANGE THIS LINE -->
            <a href="collector-dashboard.php" class="role-link">
                COLLECTOR DASHBOARD →
            </a>
        </div>

                <!-- Admin Card -->
        <div class="role-card admin">
            <i class="fas fa-chart-pie role-icon"></i>
            <h3>Admin</h3>
            <ul class="role-options">
                <li><i class="fas fa-dashboard"></i> View Dashboard and Reports</li>
                <li><i class="fas fa-flag"></i> Manage Issues</li>
                <li><i class="fas fa-users"></i> Monitor Users</li>
                <li><i class="fas fa-bell"></i> Send Notifications</li>
            </ul>
            <a href="admin-dashboard.php" class="role-link">
                ADMIN DASHBOARD →
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>