<?php
// villager-dashboard.php - Villager Main Dashboard
require_once 'config.php';
requireLogin();

// Check if user is villager
if (getUserRole() !== 'villager') {
    header('Location: role-selection.php');
    exit;
}

// Get today's collection status for this villager
$today_status = getVillagerTodayStatus(getUserId());

// Set current date
$current_date = date('l, F d Y');
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

    <!-- Today's Collection Status -->
    <?php
    $st    = $today_status ? $today_status['status'] : 'pending';
    $icons = ['pending' => 'fa-clock', 'completed' => 'fa-check-circle', 'missed' => 'fa-times-circle', 'no_waste' => 'fa-ban'];
    $labels = ['pending' => 'Pending', 'completed' => 'Collected', 'missed' => 'Missed', 'no_waste' => 'No Waste Today'];
    $colors = ['pending' => '#f57c00', 'completed' => '#2e7d32', 'missed' => '#c62828', 'no_waste' => '#0288d1'];
    ?>
    <div class="collection-status-card" style="border-left: 5px solid <?php echo $colors[$st]; ?>">
        <div class="status-label">Today's Collection Status</div>
        <div class="status-value" style="color: <?php echo $colors[$st]; ?>">
            <i class="fas <?php echo $icons[$st]; ?>"></i>
            <?php echo $labels[$st]; ?>
        </div>
        <?php if ($today_status && $today_status['collector_name']): ?>
            <div class="status-collector">
                <i class="fas fa-truck"></i> Collector: <?php echo htmlspecialchars($today_status['collector_name']); ?>
            </div>
        <?php endif; ?>
        <?php if ($st === 'pending'): ?>
            <div class="status-note">Your garbage has not been collected yet today.</div>
        <?php elseif ($st === 'missed'): ?>
            <div class="status-note">Collection was missed. Please ensure your bin is accessible next time.</div>
        <?php endif; ?>
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
        <a href="delete_account.php" class="action-card danger-card">
            <i class="fas fa-user-times"></i> Delete Account
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
<style>
/* Collection Status Card */
.collection-status-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
    border-left: 5px solid #f57c00;
}

.status-label {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #888;
    margin-bottom: 0.5rem;
}

.status-value {
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.4rem;
}

.status-collector {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 0.3rem;
}

.status-note {
    font-size: 0.88rem;
    color: #888;
    font-style: italic;
}

/* Villager Dashboard */
.dashboard {
    background: white;
    border-radius: 30px;
    padding: 3rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.dashboard h2 {
    color: #2e7d32;
    font-size: 2.2rem;
    margin-bottom: 0.5rem;
}

.dashboard h3 {
    color: #666;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.current-date {
    background: #f5f5f5;
    display: inline-block;
    padding: 0.8rem 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    font-weight: 600;
    color: #2e7d32;
}

.next-collection-card {
    background: linear-gradient(135deg, #8bc34a, #6a994e);
    color: white;
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2.5rem;
    box-shadow: 0 20px 40px rgba(139, 195, 74, 0.3);
}

.collection-datetime {
    font-size: 1.6rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.collection-location {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 10px;
    line-height: 1.6;
    opacity: 0.95;
}

.waste-type-buttons {
    display: flex;
    gap: 1.2rem;
}

.waste-btn {
    padding: 1rem 2.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1.2rem;
    cursor: default;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    gap: 10px;
}

.waste-btn.dry {
    background: rgba(139, 195, 74, 0.3);
}

.waste-btn.wet {
    background: rgba(33, 150, 243, 0.3);
}

.villager-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.action-card {
    background: #f5f5f5;
    padding: 2rem 1rem;
    border-radius: 15px;
    text-align: center;
    text-decoration: none;
    color: #333;
    font-weight: 600;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    cursor: pointer;
}

.action-card i {
    font-size: 2.5rem;
    color: #8bc34a;
}

.action-card:hover {
    background: #8bc34a;
    color: white;
    transform: translateY(-5px);
    box-shadow: 0 20px 30px rgba(139, 195, 74, 0.3);
}

.action-card:hover i {
    color: white;
}

.danger-card i { color: #e53935 !important; }

.danger-card:hover {
    background: #e53935 !important;
    color: white !important;
    box-shadow: 0 20px 30px rgba(229, 57, 53, 0.3) !important;
}

.danger-card:hover i { color: white !important; }

/* Responsive Design */
@media (max-width: 768px) {
    .villager-actions {
        grid-template-columns: 1fr;
    }

    .waste-type-buttons {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .dashboard {
        padding: 1.5rem;
    }

    .next-collection-card {
        padding: 1.5rem;
    }
}
</style>