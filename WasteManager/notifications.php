<?php
// notifications.php - View notifications for all users
require_once 'config.php';
requireLogin();

$username = $_SESSION['user'];
$user_role = getUserRole();
$user_name = getUserName();

// Mark all as read if requested
if (isset($_GET['mark_read'])) {
    markAllNotificationsRead($username);
    header('Location: notifications.php');
    exit;
}

// Get all notifications for this user (including read)
$all_notifications = getAllUserNotifications($username);
// Sort by date, newest first
usort($all_notifications, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

include 'header.php';
?>

<div class="notifications-page">
    <div class="notifications-header">
        <div>
            <h2><i class="fas fa-bell"></i> Notifications</h2>
            <p class="welcome-text">Welcome, <?php echo $user_name; ?>!</p>
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
                <div class="notification-card <?php echo $notification['type']; ?> <?php echo !$notification['read'] ? 'unread' : ''; ?>">
                    <div class="notification-icon">
                        <i class="fas fa-<?php 
                            echo $notification['type'] === 'success' ? 'check-circle' : 
                                ($notification['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle'); 
                        ?>"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-header">
                            <h4><?php echo htmlspecialchars($notification['title']); ?></h4>
                            <?php if (!$notification['read']): ?>
                                <span class="unread-badge">New</span>
                            <?php endif; ?>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                        <div class="notification-footer">
                            <small><i class="far fa-clock"></i> <?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?></small>
                            <?php if (!$notification['read']): ?>
                                <button class="btn-mark-read-small" onclick="markAsRead('<?php echo $notification['id']; ?>')">
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
}

.btn-mark-read:hover {
    background: #2e7d32;
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
}

.btn-mark-read-small {
    padding: 0.3rem 1rem;
    background: #e0e0e0;
    border: none;
    border-radius: 5px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s;
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

/* Responsive */
@media (max-width: 768px) {
    .notifications-page {
        padding: 1.5rem;
    }
    
    .notifications-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .notification-card {
        flex-direction: column;
    }
    
    .notification-icon {
        text-align: left;
    }
}
</style>

<script>
function markAsRead(notificationId) {
    // In a real app, you would send an AJAX request
    // For demo, we'll reload with a parameter
    window.location.href = 'notifications.php?mark_read=' + notificationId;
}
</script>

<?php include 'footer.php'; ?>