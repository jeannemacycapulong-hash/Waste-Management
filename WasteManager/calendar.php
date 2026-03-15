<?php
// calendar.php - Weekly Calendar View for Waste Collection
require_once 'config.php';
requireLogin();

// Get current date info
$current_date = date('Y-m-d');
$current_day = date('l');
$current_month = date('F Y');

// Default collection days (can be configured per village)
// For demo, we'll set collection on Mondays and Thursdays
$collection_days = ['Monday', 'Thursday'];

// Determine user role based on username
$username = $_SESSION['user'];
$user_role = 'villager'; // default

// Map usernames to roles (based on config.php)
if ($username === 'collector') {
    $user_role = 'collector';
} elseif ($username === 'admin') {
    $user_role = 'admin';
}
// user1 and villager remain as 'villager'

// Determine which week to show (current week by default)
$week_offset = isset($_GET['week']) ? (int)$_GET['week'] : 0;
$start_of_week = strtotime("monday this week + " . $week_offset . " weeks");
$end_of_week = strtotime("sunday this week + " . $week_offset . " weeks");

// Generate week dates
$week_dates = [];
for ($i = 0; $i < 7; $i++) {
    $day_timestamp = strtotime("+" . $i . " days", $start_of_week);
    $week_dates[] = [
        'day_name' => date('l', $day_timestamp),
        'date' => date('Y-m-d', $day_timestamp),
        'display_date' => date('M j, Y', $day_timestamp),
        'is_today' => (date('Y-m-d', $day_timestamp) == $current_date),
        'has_collection' => in_array(date('l', $day_timestamp), $collection_days)
    ];
}

// Previous and next week links
$prev_week = $week_offset - 1;
$next_week = $week_offset + 1;

// Determine back link based on user role
$back_link = 'villager-dashboard.php'; // default
$back_text = 'Back to Dashboard';

if ($user_role === 'collector') {
    $back_link = 'collector-dashboard.php';
    $back_text = 'Back to Collector Dashboard';
} elseif ($user_role === 'admin') {
    $back_link = 'admin-dashboard.php';  // Changed from placeholder.php to admin-dashboard.php
    $back_text = 'Back to Admin Dashboard';
}

include 'header.php';
?>

<div class="calendar-container">
    <div class="calendar-header">
        <h2><i class="fas fa-calendar-alt"></i> Weekly Collection Schedule</h2>
        <div class="week-navigation">
            <a href="?week=<?php echo $prev_week; ?>" class="nav-btn">
                <i class="fas fa-chevron-left"></i> Previous Week
            </a>
            <span class="current-week">
                Week of <?php echo date('M j', $start_of_week); ?> - <?php echo date('M j, Y', $end_of_week); ?>
            </span>
            <a href="?week=<?php echo $next_week; ?>" class="nav-btn">
                Next Week <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        
        <!-- FIXED: Dynamic back link based on user role -->
        <a href="<?php echo $back_link; ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> <?php echo $back_text; ?>
        </a>
    </div>

    <div class="collection-info">
        <div class="info-badge">
            <span class="badge collection-day"><i class="fas fa-trash"></i> Collection Days</span>
            <span class="badge no-collection"><i class="fas fa-times-circle"></i> No Collection</span>
        </div>
        <p class="collection-note">
            <i class="fas fa-info-circle"></i> 
            Collection for Baranggay Pampang Purok, Angeles City occurs on: 
            <strong><?php echo implode(' and ', $collection_days); ?></strong> at 8:30 AM
        </p>
    </div>

    <div class="calendar-grid">
        <!-- Day headers -->
        <?php foreach ($week_dates as $day): ?>
            <div class="calendar-day-header <?php echo $day['is_today'] ? 'today' : ''; ?>">
                <div class="day-name"><?php echo substr($day['day_name'], 0, 3); ?></div>
                <div class="day-date"><?php echo date('j', strtotime($day['date'])); ?></div>
            </div>
        <?php endforeach; ?>

        <!-- Collection status -->
        <?php foreach ($week_dates as $day): ?>
            <div class="calendar-day <?php echo $day['has_collection'] ? 'collection-day' : 'no-collection'; ?> 
                                      <?php echo $day['is_today'] ? 'today' : ''; ?>">
                <?php if ($day['has_collection']): ?>
                    <div class="collection-status">
                        <i class="fas fa-trash-alt fa-2x"></i>
                        <span class="status-text">Collection Day</span>
                        <span class="collection-time">8:30 AM</span>
                        <div class="waste-types">
                            <span class="waste-tag dry">Dry</span>
                            <span class="waste-tag wet">Wet</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-collection-status">
                        <i class="fas fa-check-circle fa-2x"></i>
                        <span class="status-text">No Collection</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Monthly Overview Section -->
    <div class="monthly-overview">
        <h3><i class="fas fa-calendar"></i> <?php echo $current_month; ?> Overview</h3>
        
        <?php
        // Generate current month calendar
        $month_start = strtotime('first day of this month');
        $month_end = strtotime('last day of this month');
        $days_in_month = date('t', $month_start);
        $first_day_of_month = date('w', $month_start); // 0 for Sunday, 6 for Saturday
        
        // Adjust for Monday as first day (1 for Monday, 0 for Sunday)
        $first_day_offset = ($first_day_of_month == 0) ? 6 : $first_day_of_month - 1;
        ?>
        
        <div class="month-calendar">
            <div class="month-weekdays">
                <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span>
                <span>Fri</span><span>Sat</span><span>Sun</span>
            </div>
            <div class="month-days">
                <!-- Empty cells for days before month starts -->
                <?php for ($i = 0; $i < $first_day_offset; $i++): ?>
                    <div class="month-day empty"></div>
                <?php endfor; ?>
                
                <!-- Days of the month -->
                <?php for ($day = 1; $day <= $days_in_month; $day++): 
                    $current_day_date = date('Y-m-d', strtotime(date('Y-m') . '-' . $day));
                    $day_name = date('l', strtotime($current_day_date));
                    $has_collection = in_array($day_name, $collection_days);
                    $is_current_day = ($current_day_date == $current_date);
                ?>
                    <div class="month-day <?php echo $has_collection ? 'collection-month-day' : ''; ?> 
                                           <?php echo $is_current_day ? 'current-day' : ''; ?>">
                        <span class="day-number"><?php echo $day; ?></span>
                        <?php if ($has_collection): ?>
                            <i class="fas fa-trash-alt collection-icon" title="Collection Day"></i>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="month-legend">
            <span><i class="fas fa-trash-alt" style="color: #8bc34a;"></i> Collection Days</span>
            <span><i class="fas fa-circle" style="color: #2e7d32;"></i> Today</span>
        </div>
    </div>

    <!-- Upcoming Collections List -->
    <div class="upcoming-collections">
        <h3><i class="fas fa-list"></i> Upcoming Collections</h3>
        <div class="collections-list">
            <?php
            // Show next 5 collection days
            $collection_count = 0;
            $check_date = strtotime('today');
            
            while ($collection_count < 5) {
                $check_date = strtotime('+1 day', $check_date);
                $check_day_name = date('l', $check_date);
                
                if (in_array($check_day_name, $collection_days)) {
                    $collection_count++;
                    $formatted_date = date('l, F j, Y', $check_date);
                    $days_from_now = (strtotime(date('Y-m-d', $check_date)) - strtotime($current_date)) / (60 * 60 * 24);
                    
                    if ($days_from_now == 0) {
                        $when = "Today";
                    } elseif ($days_from_now == 1) {
                        $when = "Tomorrow";
                    } else {
                        $when = "In " . $days_from_now . " days";
                    }
                    ?>
                    <div class="collection-item">
                        <div class="collection-date">
                            <i class="fas fa-calendar-day"></i>
                            <strong><?php echo $formatted_date; ?></strong>
                        </div>
                        <div class="collection-when"><?php echo $when; ?></div>
                        <div class="collection-details">
                            <span class="collection-badge">8:30 AM</span>
                            <span class="collection-badge dry-badge">Dry</span>
                            <span class="collection-badge wet-badge">Wet</span>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<style>
    /* Calendar specific styles */
.calendar-container {
    background: white;
    border-radius: 30px;
    padding: 2rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
}

.calendar-header h2 {
    color: #2e7d32;
    margin: 0;
    font-size: 2rem;
}

.week-navigation {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #f5f5f5;
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

.nav-btn {
    color: #2e7d32;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    transition: all 0.3s;
}

.nav-btn:hover {
    background: #8bc34a;
    color: white;
}

.current-week {
    font-weight: 600;
    color: #2e7d32;
    min-width: 250px;
    text-align: center;
}

.collection-info {
    background: #f9f9f9;
    padding: 1.5rem;
    border-radius: 15px;
    margin-bottom: 2rem;
}

.info-badge {
    display: flex;
    gap: 2rem;
    margin-bottom: 1rem;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    font-weight: 600;
}

.badge.collection-day {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge.no-collection {
    background: #ffebee;
    color: #c62828;
}

.collection-note {
    color: #666;
    font-size: 1.1rem;
    margin: 0;
}

.collection-note i {
    color: #8bc34a;
    margin-right: 0.5rem;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1rem;
    margin-bottom: 3rem;
    background: #f5f5f5;
    padding: 1.5rem;
    border-radius: 20px;
}

.calendar-day-header {
    text-align: center;
    padding: 1rem;
    background: #e0e0e0;
    border-radius: 10px;
}

.calendar-day-header.today {
    background: #8bc34a;
    color: white;
}

.day-name {
    font-size: 1.2rem;
    font-weight: 600;
    text-transform: uppercase;
}

.day-date {
    font-size: 2rem;
    font-weight: 700;
}

.calendar-day {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    min-height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.calendar-day:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.calendar-day.collection-day {
    background: linear-gradient(135deg, #f1f8e9, #dcedc8);
    border: 2px solid #8bc34a;
}

.calendar-day.no-collection {
    background: #f5f5f5;
    border: 2px solid #e0e0e0;
}

.calendar-day.today {
    border: 4px solid #2e7d32;
}

.collection-status, .no-collection-status {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.collection-status i {
    color: #8bc34a;
}

.status-text {
    font-weight: 600;
    font-size: 1rem;
}

.collection-time {
    background: #8bc34a;
    color: white;
    padding: 0.3rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.waste-types {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.waste-tag {
    padding: 0.2rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.waste-tag.dry {
    background: #8bc34a;
    color: white;
}

.waste-tag.wet {
    background: #2196F3;
    color: white;
}

.no-collection-status i {
    color: #9e9e9e;
}

.no-collection-status .status-text {
    color: #9e9e9e;
}

/* Monthly Overview */
.monthly-overview {
    margin-top: 2rem;
    padding: 2rem;
    background: #f9f9f9;
    border-radius: 20px;
}

.monthly-overview h3 {
    color: #2e7d32;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.month-calendar {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.month-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #8bc34a;
    color: white;
    padding: 1rem;
    font-weight: 600;
    text-align: center;
}

.month-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #e0e0e0;
    padding: 1px;
}

.month-day {
    background: white;
    padding: 1rem;
    min-height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.month-day.empty {
    background: #f5f5f5;
}

.month-day.collection-month-day {
    background: #f1f8e9;
}

.month-day.current-day {
    border: 2px solid #2e7d32;
    font-weight: bold;
}

.day-number {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.collection-icon {
    color: #8bc34a;
    font-size: 1rem;
}

.month-legend {
    display: flex;
    gap: 2rem;
    margin-top: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 10px;
}

.month-legend span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
}

/* Upcoming Collections List */
.upcoming-collections {
    margin-top: 2rem;
}

.upcoming-collections h3 {
    color: #2e7d32;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.collections-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.collection-item {
    background: #f9f9f9;
    padding: 1.2rem;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    border-left: 5px solid #8bc34a;
}

.collection-date {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 1.1rem;
    color: #2e7d32;
}

.collection-when {
    background: #2e7d32;
    color: white;
    padding: 0.3rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.collection-details {
    display: flex;
    gap: 0.8rem;
}

.collection-badge {
    padding: 0.3rem 1rem;
    border-radius: 5px;
    font-size: 0.9rem;
    font-weight: 600;
    background: #e0e0e0;
    color: #666;
}

.collection-badge.dry-badge {
    background: #8bc34a;
    color: white;
}

.collection-badge.wet-badge {
    background: #2196F3;
    color: white;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .calendar-grid {
        gap: 0.5rem;
        padding: 1rem;
    }
    
    .day-date {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .calendar-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .week-navigation {
        width: 100%;
        justify-content: space-between;
    }
    
    .current-week {
        min-width: auto;
        font-size: 0.9rem;
    }
    
    .calendar-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .calendar-day-header {
        display: none;
    }
    
    .calendar-day {
        min-height: auto;
    }
    
    .collection-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .collection-details {
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 480px) {
    .calendar-container {
        padding: 1rem;
    }
    
    .info-badge {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .month-days {
        grid-template-columns: repeat(7, 1fr);
    }
    
    .month-day {
        padding: 0.5rem;
        min-height: 60px;
    }
    
    .day-number {
        font-size: 1rem;
    }
}
</style>