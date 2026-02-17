<?php
// villager-dashboard.php - Villager Main Dashboard
require_once 'config.php';
requireLogin();
// Set current date
$current_date = "Thursday, February 05 2026";
include 'header.php';
?>

<div class="dashboard villager-dashboard">
    <h2>Waste Management App for Villager</h2>
    <h3><i class="fas fa-calendar-day"></i> Garbage Collection Schedule</h3>
    <p class="current-date">
        <i class="far fa-calendar-alt"></i> <?php echo $current_date; ?>
    </p>

    <div class="next-collection-card">
        <div class="collection-datetime">
            <i class="fas fa-clock"></i> <strong>Thursday, 8:30 AM</strong>
        </div>
        <div class="collection-location">
            <i class="fas fa-map-marker-alt"></i> 
            Baranggay Pampang Purok, Angeles City Pampang Bario
        </div>
        <div class="waste-type-buttons">
            <span class="waste-btn dry"><i class="fas fa-leaf"></i> Dry</span>
            <span class="waste-btn wet"><i class="fas fa-water"></i> Wet</span>
        </div>
    </div>

    <div class="villager-actions">
        <a href="calendar.php" class="action-card">
            <i class="fas fa-calendar-week"></i> Weekly View
        </a>
        <a href="villager-report-issue.php" class="action-card">
            <i class="fas fa-exclamation-triangle"></i> Report Issue
        </a>
        <a href="monthly-dues.php" class="action-card">
            <i class="fas fa-coins"></i> Monthly Dues
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>