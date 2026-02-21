<?php
// notifications.php - View notifications for all users
require_once 'config.php';
requireLogin();

$username = $_SESSION['user'];
$user_role = getUserRole();
$user_name = getUserName();
$user_id = getUserId(); // Get the numeric user ID from session

// Mark all as read if requested
if (isset($_GET['mark_read'])) {
    if ($_GET['mark_read'] == '1') {
        // Mark all as read
        markAllNotificationsRead($user_id);
    } else {
        // Mark single notification as read
        markNotificationRead($_GET['mark_read']);
    }
    header('Location: notifications.php');
    exit;
}

// Get all notifications for this user (including read)
// Use user_id for database, fallback to username for session
if ($user_id > 0) {
    $all_notifications = getAllUserNotifications($user_id);
} else {
    // Fallback to old method using username
    $all_notifications = array_filter($_SESSION['notifications'] ?? [], function($n) use ($username) {
        return $n['user_id'] == $username;
    });
}

// Sort by date, newest first (database already sorts, but this handles session fallback)
if (!empty($all_notifications) && isset($all_notifications[0]['created_at'])) {
    usort($all_notifications, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

include 'header.php';
?>

<div class="notifications-page">
    <div class="notifications-header">
        <div>
            <h2><i class="fas fa-bell"></i> Notifications</h2>
            <p class="welcome-text">Welcome, <?php echo htmlspecialchars($user_name); ?>!</p>
        </div>
        <div class="header-actions">
            <?php if (!empty($all_notifications)): ?>
                <a href="?mark_read=1" class="btn-mark-read">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </a>
            <?php endif; ?>
            <a href="<?php 
                if ($user_role === 'villager') echo 'villager-dashboard.php';
                elseif ($user_role === 'collector') echo 'collector-dashboard.php';
                else echo 'admin-dashboard.php';
            ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="notifications-list">
        <?php if (empty($all_notifications)): ?>
            <div class="no-notifications">
                <i class="fas fa-bell-slash fa-4x"></i>
                <p>No notifications yet</p>
                <p class="sub-text">When you receive notifications, they'll appear here</p>
            </div>
        <?php else: ?>
            <?php foreach ($all_notifications as $notification): ?>
                <?php 
                // Handle both database and session notification formats
                $is_read = isset($notification['is_read']) ? $notification['is_read'] : ($notification['read'] ?? false);
                $type = $notification['type'] ?? 'info';
                $title = $notification['title'] ?? 'Notification';
                $message = $notification['message'] ?? '';
                $created_at = $notification['created_at'] ?? date('Y-m-d H:i:s');
                $notif_id = $notification['id'] ?? '';
                ?>
                <div class="notification-card <?php echo $type; ?> <?php echo !$is_read ? 'unread' : ''; ?>">
                    <div class="notification-icon">
                        <i class="fas fa-<?php 
                            echo $type === 'success' ? 'check-circle' : 
                                ($type === 'warning' ? 'exclamation-triangle' : 'info-circle'); 
                        ?>"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-header">
                            <h4><?php echo htmlspecialchars($title); ?></h4>
                            <?php if (!$is_read): ?>
                                <span class="unread-badge">New</span>
                            <?php endif; ?>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($message)); ?></p>
                        <div class="notification-footer">
                            <small><i class="far fa-clock"></i> <?php echo date('M d, Y h:i A', strtotime($created_at)); ?></small>
                            <?php if (!$is_read): ?>
                                <button class="btn-mark-read-small" onclick="markAsRead('<?php echo $notif_id; ?>')">
                                    Mark as Read
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.notifications-page {
    background: white;
    border-radius: 30px;
    padding: 2rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.notifications-header h2 {
    color: #2e7d32;
    margin: 0;
    font-size: 2rem;
}

.welcome-text {
    color: #666;
    margin: 0.5rem 0 0;
}

.header-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-mark-read {
    padding: 0.8rem 1.5rem;
    background: #8bc34a;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-mark-read:hover {
    background: #2e7d32;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
}

.btn-back {
    padding: 0.8rem 1.5rem;
    background: #f5f5f5;
    color: #2e7d32;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-back:hover {
    background: #8bc34a;
    color: white;
}

.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.notification-card {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    background: #f9f9f9;
    border-radius: 15px;
    border-left: 5px solid transparent;
    transition: all 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.notification-card:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.notification-card.unread {
    background: #e8f5e9;
    border-left-width: 5px;
}

.notification-card.info { border-left-color: #2196F3; }
.notification-card.success { border-left-color: #8bc34a; }
.notification-card.warning { border-left-color: #ff9800; }

.notification-card.unread.info { background: #e3f2fd; }
.notification-card.unread.success { background: #e8f5e9; }
.notification-card.unread.warning { background: #fff3e0; }

.notification-icon {
    font-size: 2rem;
    min-width: 50px;
    text-align: center;
}

.notification-card.info .notification-icon { color: #2196F3; }
.notification-card.success .notification-icon { color: #8bc34a; }
.notification-card.warning .notification-icon { color: #ff9800; }

.notification-content {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.notification-header h4 {
    color: #333;
    margin: 0;
    font-size: 1.1rem;
}

.unread-badge {
    background: #f44336;
    color: white;
    padding: 0.2rem 0.8rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.notification-content p {
    color: #555;
    margin: 0 0 1rem;
    line-height: 1.5;
}

.notification-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.notification-footer small {
    color: #999;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.btn-mark-read-small {
    padding: 0.3rem 1rem;
    background: #e0e0e0;
    border: none;
    border-radius: 5px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.btn-mark-read-small:hover {
    background: #8bc34a;
    color: white;
}

.no-notifications {
    text-align: center;
    padding: 4rem 2rem;
    color: #999;
}

.no-notifications i {
    color: #ddd;
    margin-bottom: 1rem;
}

.no-notifications .sub-text {
    color: #bbb;
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

/* Animation for new notifications */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-card {
    animation: fadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .notifications-page {
        padding: 1.5rem;
    }
    
    .notifications-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .btn-mark-read, .btn-back {
        width: 100%;
        justify-content: center;
    }
    
    .notification-card {
        flex-direction: column;
    }
    
    .notification-icon {
        text-align: left;
    }
    
    .notification-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .btn-mark-read-small {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function markAsRead(notificationId) {
    if (notificationId) {
        window.location.href = 'notifications.php?mark_read=' + notificationId;
    }
}

// Optional: Auto-refresh notifications every 30 seconds (optional)
/*
setTimeout(function() {
    location.reload();
}, 30000);
*/
</script>

<?php include 'footer.php'; ?>