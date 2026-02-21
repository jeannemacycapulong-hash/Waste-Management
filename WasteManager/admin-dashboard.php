<?php
// admin-dashboard.php - Admin Main Dashboard
require_once 'config.php';
requireLogin();

// Check if user is admin
if (getUserRole() !== 'admin') {
    header('Location: role-selection.php');
    exit;
}

// Get statistics
$total_villagers = 0;
$total_collectors = 0;
foreach ($GLOBALS['valid_users'] as $user => $data) {
    if ($data['role'] === 'villager') $total_villagers++;
    if ($data['role'] === 'collector') $total_collectors++;
}

// Get pickup statistics
$pickup_stats = getPickupStats();

// Get all reports
$all_reports = getAllReports();
$pending_reports = count(array_filter($all_reports, fn($r) => $r['status'] === 'pending'));
$resolved_reports = count(array_filter($all_reports, fn($r) => $r['status'] === 'resolved'));

// Get recent reports (last 5)
$recent_reports = array_slice($all_reports, 0, 5);

// Get unread notifications count for admin
$admin_notifications = getUserNotifications('admin');

include 'header.php';
?>

<!-- Rest of your HTML remains exactly the same -->

<div class="admin-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>Admin Dashboard</h2>
            <p class="welcome-text">Welcome back, <?php echo getUserName(); ?>!</p>
        </div>
        <div class="header-actions">
            <div class="notifications-dropdown">
                <button class="notifications-btn" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    <?php if (count($admin_notifications) > 0): ?>
                        <span class="badge"><?php echo count($admin_notifications); ?></span>
                    <?php endif; ?>
                </button>
                <div id="notificationsPanel" class="notifications-panel" style="display: none;">
                    <h4>Notifications</h4>
                    <?php if (empty($admin_notifications)): ?>
                        <p class="no-notifications">No new notifications</p>
                    <?php else: ?>
                        <?php foreach ($admin_notifications as $notification): ?>
                            <div class="notification-item <?php echo $notification['type']; ?>">
                                <strong><?php echo $notification['title']; ?></strong>
                                <p><?php echo $notification['message']; ?></p>
                                <small><?php echo date('M d, H:i', strtotime($notification['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <span class="date-display"><?php echo date('l, F j, Y'); ?></span>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="metric-info">
                <span class="metric-value"><?php echo $total_villagers + $total_collectors; ?></span>
                <span class="metric-label">Total Users</span>
            </div>
            <div class="metric-breakdown">
                <span><i class="fas fa-home"></i> Villagers: <?php echo $total_villagers; ?></span>
                <span><i class="fas fa-truck"></i> Collectors: <?php echo $total_collectors; ?></span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon green">
                <i class="fas fa-trash-alt"></i>
            </div>
            <div class="metric-info">
                <span class="metric-value"><?php echo $pickup_stats['completed']; ?>/<?php echo $pickup_stats['total']; ?></span>
                <span class="metric-label">Today's Pickups</span>
            </div>
            <div class="metric-breakdown">
                <span><i class="fas fa-check-circle"></i> Completed: <?php echo $pickup_stats['completed']; ?></span>
                <span><i class="fas fa-clock"></i> Pending: <?php echo $pickup_stats['pending']; ?></span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="metric-info">
                <span class="metric-value"><?php echo $pending_reports; ?></span>
                <span class="metric-label">Pending Reports</span>
            </div>
            <div class="metric-breakdown">
                <span><i class="fas fa-check-circle"></i> Resolved: <?php echo $resolved_reports; ?></span>
                <span><i class="fas fa-clock"></i> Pending: <?php echo $pending_reports; ?></span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon purple">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="metric-info">
                <span class="metric-value"><?php echo $pickup_stats['missed'] + $pickup_stats['no_waste']; ?></span>
                <span class="metric-label">Exceptions</span>
            </div>
            <div class="metric-breakdown">
                <span><i class="fas fa-times-circle"></i> Missed: <?php echo $pickup_stats['missed']; ?></span>
                <span><i class="fas fa-ban"></i> No Waste: <?php echo $pickup_stats['no_waste']; ?></span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
        <div class="action-grid">
            <a href="calendar.php" class="action-card">
                <i class="fas fa-calendar-week"></i>
                <span>Weekly Calendar View</span>
            </a>
            <a href="#reports" onclick="showTab('reports')" class="action-card">
                <i class="fas fa-flag"></i>
                <span>Manage Reports</span>
            </a>
            <a href="#pickups" onclick="showTab('pickups')" class="action-card">
                <i class="fas fa-clipboard-check"></i>
                <span>Monitor Pickups</span>
            </a>
            <a href="#notifications" onclick="openNotificationModal()" class="action-card">
                <i class="fas fa-bell"></i>
                <span>Send Notification</span>
            </a>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="admin-tabs">
        <button class="tab-btn active" onclick="showTab('reports')">Reports Management</button>
        <button class="tab-btn" onclick="showTab('pickups')">Pickup Monitoring</button>
        <button class="tab-btn" onclick="showTab('users')">User Management</button>
    </div>

    <!-- Reports Tab -->
    <div id="reports-tab" class="tab-content active">
        <div class="reports-filters">
            <select id="reportTypeFilter" onchange="filterReports()">
                <option value="all">All Reports</option>
                <option value="villager">Villager Reports</option>
                <option value="collector">Collector Reports</option>
            </select>
            <select id="reportStatusFilter" onchange="filterReports()">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="resolved">Resolved</option>
            </select>
        </div>

        <div class="reports-list">
            <?php foreach ($all_reports as $report): ?>
                <div class="report-card <?php echo $report['status']; ?>" 
                    data-type="<?php echo $report['reporter_type'] ?? $report['type'] ?? 'villager'; ?>" 
                    data-status="<?php echo $report['status']; ?>">
                    
                    <div class="report-header">
                        <?php 
                        // Determine report type (database vs session format)
                        $report_type = $report['reporter_type'] ?? $report['type'] ?? 'villager';
                        $report_type_display = ucfirst($report_type);
                        $report_type_icon = ($report_type === 'villager') ? 'home' : 'truck';
                        ?>
                        <div class="report-type-badge <?php echo $report_type; ?>">
                            <i class="fas fa-<?php echo $report_type_icon; ?>"></i>
                            <?php echo $report_type_display; ?> Report
                        </div>
                        <span class="report-status <?php echo $report['status']; ?>">
                            <?php echo ucfirst($report['status']); ?>
                        </span>
                    </div>
                    
                    <div class="report-body">
                        <p><strong>From:</strong> 
                            <?php 
                            // Handle different possible reporter fields
                            if (isset($report['reporter_name'])) {
                                echo $report['reporter_name'];
                            } elseif (isset($report['name'])) {
                                echo $report['name'];
                            } else {
                                echo 'Unknown';
                            }
                            
                            // Show reporter identifier if available
                            if (isset($report['username'])) {
                                echo ' (' . $report['username'] . ')';
                            } elseif (isset($report['reporter'])) {
                                echo ' (' . $report['reporter'] . ')';
                            }
                            ?>
                        </p>
                        
                        <p><strong>Type:</strong> 
                            <?php 
                            if (isset($report['issue_type'])) {
                                echo str_replace('_', ' ', ucfirst($report['issue_type']));
                            } else {
                                echo 'General';
                            }
                            ?>
                        </p>
                        
                        <p><strong>Location:</strong> <?php echo $report['location'] ?? 'N/A'; ?></p>
                        
                        <p><strong>Description:</strong> 
                            <?php 
                            if (!empty($report['description'])) {
                                echo nl2br(htmlspecialchars($report['description']));
                            } elseif (!empty($report['message'])) {
                                echo nl2br(htmlspecialchars($report['message']));
                            } else {
                                echo 'No description';
                            }
                            ?>
                        </p>
                        
                        <p><strong>Urgency:</strong> 
                            <span class="urgency-tag <?php echo $report['urgency'] ?? 'low'; ?>">
                                <?php echo ucfirst($report['urgency'] ?? 'low'); ?>
                            </span>
                        </p>
                        
                        <p><strong>Reported:</strong> 
                            <?php 
                            if (isset($report['created_at'])) {
                                echo date('M d, Y h:i A', strtotime($report['created_at']));
                            } else {
                                echo 'Unknown date';
                            }
                            ?>
                        </p>
                        
                        <?php if ($report['status'] === 'resolved' && !empty($report['admin_response'])): ?>
                            <div class="admin-response">
                                <strong>Admin Response:</strong>
                                <p><?php echo nl2br(htmlspecialchars($report['admin_response'])); ?></p>
                                <?php if (isset($report['resolved_at'])): ?>
                                    <small>Resolved: <?php echo date('M d, Y h:i A', strtotime($report['resolved_at'])); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($report['status'] === 'pending'): ?>
                        <div class="report-actions">
                            <button class="btn-resolve" onclick="openResolveModal('<?php echo $report['id']; ?>')">
                                <i class="fas fa-check-circle"></i> Resolve Issue
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                        
                        <?php if ($report['status'] === 'resolved'): ?>
                            <div class="admin-response">
                                <strong>Admin Response:</strong>
                                <p><?php echo $report['admin_response']; ?></p>
                                <small>Resolved: <?php echo date('M d, Y h:i A', strtotime($report['resolved_at'])); ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($report['status'] === 'pending'): ?>
                        <div class="report-actions">
                            <button class="btn-resolve" onclick="openResolveModal('<?php echo $report['id']; ?>')">
                                <i class="fas fa-check-circle"></i> Resolve Issue
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($all_reports)): ?>
                <p class="no-data">No reports found</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pickup Monitoring Tab -->
    <div id="pickups-tab" class="tab-content">
        <div class="pickup-stats">
            <span class="stat-badge all">Total: <?php echo $pickup_stats['total']; ?></span>
            <span class="stat-badge pending">Pending: <?php echo $pickup_stats['pending']; ?></span>
            <span class="stat-badge completed">Completed: <?php echo $pickup_stats['completed']; ?></span>
            <span class="stat-badge missed">Missed: <?php echo $pickup_stats['missed']; ?></span>
            <span class="stat-badge no-waste">No Waste: <?php echo $pickup_stats['no_waste']; ?></span>
        </div>

        <div class="pickups-list">
            <table class="pickups-table">
                <thead>
                    <tr>
                        <th>Villager</th>
                        <th>Address</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Collector</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['pickup_statuses'] as $pickup): ?>
                        <tr>
                            <td><?php echo $pickup['villager']; ?></td>
                            <td><?php echo $pickup['address']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($pickup['date'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $pickup['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $pickup['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo getUserDisplayName($pickup['collector']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- User Management Tab -->
    <div id="users-tab" class="tab-content">
        <div class="users-grid">
            <div class="user-section">
                <h4><i class="fas fa-home"></i> Villagers</h4>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($GLOBALS['valid_users'] as $username => $data): ?>
                            <?php if ($data['role'] === 'villager'): ?>
                                <tr>
                                    <td><?php echo $username; ?></td>
                                    <td><?php echo $data['name']; ?></td>
                                    <td><span class="status-badge active">Active</span></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="user-section">
                <h4><i class="fas fa-truck"></i> Collectors</h4>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($GLOBALS['valid_users'] as $username => $data): ?>
                            <?php if ($data['role'] === 'collector'): ?>
                                <tr>
                                    <td><?php echo $username; ?></td>
                                    <td><?php echo $data['name']; ?></td>
                                    <td><span class="status-badge active">Active</span></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Resolve Issue Modal -->
<div id="resolveModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Resolve Issue</h3>
            <span class="close" onclick="closeResolveModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="resolveForm" onsubmit="resolveIssue(event)">
                <input type="hidden" id="reportId" name="reportId" value="">
                
                <div class="form-group">
                    <label for="responseMessage">Response Message <span class="required">*</span></label>
                    <textarea id="responseMessage" name="responseMessage" rows="4" required 
                        placeholder="Write your response to the reporter..."></textarea>
                </div>

                <div class="form-group">
                    <label>Send Notification To:</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" id="notifyReporter" value="reporter" checked disabled> Reporter Only</label>
                        <label><input type="checkbox" id="notifyAll" name="notifyAll" value="all"> All Users (Broadcast)</label>
                    </div>
                </div>

                <button type="submit" class="btn-resolve-submit">
                    <i class="fas fa-paper-plane"></i> Send Response & Resolve
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Send Notification Modal -->
<div id="notificationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-bell"></i> Send Broadcast Notification</h3>
            <span class="close" onclick="closeNotificationModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="notificationForm" onsubmit="sendNotification(event)">
                <div class="form-group">
                    <label>Send To <span class="required">*</span></label>
                    <select id="notificationTarget" required onchange="toggleSpecificUser()">
                        <option value="">-- Select Recipients --</option>
                        <option value="all">All Users</option>
                        <option value="villagers">All Villagers</option>
                        <option value="collectors">All Collectors</option>
                        <option value="specific">Specific User</option>
                    </select>
                </div>

                <div id="specificUserField" class="form-group" style="display: none;">
                    <label>Select User</label>
                    <select id="specificUser">
                        <option value="">-- Select User --</option>
                        <?php foreach ($GLOBALS['valid_users'] as $username => $data): ?>
                            <option value="<?php echo $username; ?>"><?php echo $data['name']; ?> (<?php echo $data['role']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notificationTitle">Title <span class="required">*</span></label>
                    <input type="text" id="notificationTitle" required placeholder="e.g., Schedule Update">
                </div>

                <div class="form-group">
                    <label for="notificationMessage">Message <span class="required">*</span></label>
                    <textarea id="notificationMessage" rows="4" required 
                        placeholder="Type your notification message..."></textarea>
                </div>

                <div class="form-group">
                    <label>Notification Type</label>
                    <div class="radio-group">
                        <label><input type="radio" name="notifType" value="info" checked> Info</label>
                        <label><input type="radio" name="notifType" value="success"> Success</label>
                        <label><input type="radio" name="notifType" value="warning"> Warning</label>
                    </div>
                </div>

                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i> Send Notification
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* Admin Dashboard Specific Styles */
.admin-dashboard {
    background: white;
    border-radius: 30px;
    padding: 2rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.dashboard-header h2 {
    color: #2e7d32;
    font-size: 2rem;
    margin: 0;
}

.welcome-text {
    color: #666;
    margin: 0.5rem 0 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.notifications-btn {
    background: #f5f5f5;
    border: none;
    padding: 0.8rem 1.2rem;
    border-radius: 10px;
    cursor: pointer;
    position: relative;
    font-size: 1.2rem;
}

.notifications-btn .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #f44336;
    color: white;
    border-radius: 50%;
    padding: 0.2rem 0.5rem;
    font-size: 0.8rem;
}

.notifications-panel {
    position: absolute;
    right: 2rem;
    top: 5rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    width: 300px;
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
}

.notifications-panel h4 {
    padding: 1rem;
    margin: 0;
    border-bottom: 1px solid #e0e0e0;
    position: sticky;
    top: 0;
    background: white;
}

.notification-item {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.notification-item.info { border-left: 3px solid #2196F3; }
.notification-item.success { border-left: 3px solid #8bc34a; }
.notification-item.warning { border-left: 3px solid #ff9800; }

.date-display {
    color: #666;
    font-weight: 500;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.metric-card {
    background: #f9f9f9;
    padding: 1.5rem;
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.metric-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.metric-icon.blue { background: #e3f2fd; color: #1976d2; }
.metric-icon.green { background: #e8f5e9; color: #2e7d32; }
.metric-icon.orange { background: #fff3e0; color: #f57c00; }
.metric-icon.purple { background: #f3e5f5; color: #7b1fa2; }

.metric-info {
    display: flex;
    flex-direction: column;
}

.metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
}

.metric-label {
    color: #666;
    font-size: 0.95rem;
}

.metric-breakdown {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-size: 0.9rem;
    color: #666;
    padding-top: 0.5rem;
    border-top: 1px solid #e0e0e0;
}

.quick-actions {
    margin-bottom: 2rem;
}

.quick-actions h3 {
    color: #2e7d32;
    margin-bottom: 1rem;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.action-card {
    background: linear-gradient(135deg, #f5f5f5, #e8f5e9);
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    color: #2e7d32;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.action-card:hover {
    border-color: #8bc34a;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(139, 195, 74, 0.2);
}

.action-card i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
}

.action-card span {
    font-weight: 600;
}

.admin-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 0.5rem;
}

.tab-btn {
    padding: 0.8rem 1.5rem;
    background: none;
    border: none;
    font-size: 1rem;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    transition: all 0.3s;
}

.tab-btn.active {
    color: #2e7d32;
    background: #e8f5e9;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.reports-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.reports-filters select {
    padding: 0.8rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    outline: none;
    flex: 1;
}

.reports-filters select:focus {
    border-color: #8bc34a;
}

.report-card {
    background: #f9f9f9;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 5px solid transparent;
}

.report-card.pending { border-left-color: #f57c00; }
.report-card.resolved { border-left-color: #2e7d32; }

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.report-type-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.report-type-badge.villager { background: #e3f2fd; color: #1976d2; }
.report-type-badge.collector { background: #fff3e0; color: #f57c00; }

.report-status {
    padding: 0.3rem 1rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}

.report-status.pending { background: #fff3e0; color: #f57c00; }
.report-status.resolved { background: #e8f5e9; color: #2e7d32; }

.report-body p {
    margin: 0.5rem 0;
    color: #555;
}

.urgency-tag {
    padding: 0.2rem 0.8rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.urgency-tag.low { background: #e8f5e9; color: #2e7d32; }
.urgency-tag.medium { background: #fff3e0; color: #f57c00; }
.urgency-tag.high { background: #ffebee; color: #f44336; }

.admin-response {
    background: #e8f5e9;
    padding: 1rem;
    border-radius: 10px;
    margin-top: 1rem;
}

.admin-response strong {
    color: #2e7d32;
}

.report-actions {
    margin-top: 1rem;
    text-align: right;
}

.btn-resolve {
    padding: 0.8rem 1.5rem;
    background: #8bc34a;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-resolve:hover {
    background: #2e7d32;
}

.pickup-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.pickups-table {
    width: 100%;
    border-collapse: collapse;
}

.pickups-table th {
    text-align: left;
    padding: 1rem;
    background: #f5f5f5;
    color: #2e7d32;
}

.pickups-table td {
    padding: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.users-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.user-section h4 {
    color: #2e7d32;
    margin-bottom: 1rem;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th {
    text-align: left;
    padding: 0.8rem;
    background: #f5f5f5;
}

.users-table td {
    padding: 0.8rem;
    border-bottom: 1px solid #e0e0e0;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    margin: 50px auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 30px 60px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    color: #2e7d32;
    margin: 0;
}

.close {
    font-size: 2rem;
    font-weight: 700;
    color: #999;
    cursor: pointer;
}

.close:hover {
    color: #333;
}

.modal-body {
    padding: 1.5rem;
}

.btn-resolve-submit,
.btn-send {
    width: 100%;
    padding: 1rem;
    background: #8bc34a;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-resolve-submit:hover,
.btn-send:hover {
    background: #2e7d32;
}

.no-data {
    text-align: center;
    padding: 3rem;
    color: #999;
    font-style: italic;
}

/* Responsive */
@media (max-width: 992px) {
    .users-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .admin-dashboard {
        padding: 1.5rem;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-tabs {
        flex-direction: column;
    }
    
    .reports-filters {
        flex-direction: column;
    }
    
    .report-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<script>
let currentReportId = '';

function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

function filterReports() {
    const typeFilter = document.getElementById('reportTypeFilter').value;
    const statusFilter = document.getElementById('reportStatusFilter').value;
    
    document.querySelectorAll('.report-card').forEach(card => {
        const type = card.dataset.type;
        const status = card.dataset.status;
        
        let typeMatch = typeFilter === 'all' || type === typeFilter;
        let statusMatch = statusFilter === 'all' || status === statusFilter;
        
        card.style.display = typeMatch && statusMatch ? 'block' : 'none';
    });
}

function openResolveModal(reportId) {
    currentReportId = reportId;
    document.getElementById('reportId').value = reportId; // This line is crucial
    document.getElementById('resolveModal').style.display = 'block';
}

function closeResolveModal() {
    document.getElementById('resolveModal').style.display = 'none';
    document.getElementById('resolveForm').reset();
}

function resolveIssue(event) {
    event.preventDefault();
    
    const reportId = document.getElementById('reportId').value;
    const response = document.getElementById('responseMessage').value;
    const notifyAll = document.getElementById('notifyAll')?.checked || false;
    
    if (!response) {
        alert('Please enter a response message');
        return;
    }
    
    if (!reportId) {
        alert('Report ID is missing');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    // Prepare data for submission
    const formData = new FormData();
    formData.append('report_id', reportId);
    formData.append('response', response);
    formData.append('notify_all', notifyAll ? '1' : '0');
    formData.append('resolve_report', '1');
    
    // Send to server
    fetch('resolve_report.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Issue resolved! ' + (notifyAll ? 'Broadcast notification sent to all ' + data.role + 's.' : 'Reporter notified.'));
            closeResolveModal();
            
            // Show a temporary success message
            showSuccess('Report resolved successfully!');
            
            // Reload after a short delay to show updated status
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// ADD THIS HELPER FUNCTION HERE
function showSuccess(message) {
    // Create a temporary success message
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.style.position = 'fixed';
    successDiv.style.top = '20px';
    successDiv.style.right = '20px';
    successDiv.style.zIndex = '9999';
    successDiv.style.padding = '1rem 2rem';
    successDiv.style.background = '#e8f5e9';
    successDiv.style.color = '#2e7d32';
    successDiv.style.borderRadius = '10px';
    successDiv.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
    successDiv.style.animation = 'slideIn 0.3s ease-out';
    successDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
    
    document.body.appendChild(successDiv);
    
    setTimeout(() => {
        successDiv.remove();
    }, 3000);
}

function openNotificationModal() {
    document.getElementById('notificationModal').style.display = 'block';
}

function closeNotificationModal() {
    document.getElementById('notificationModal').style.display = 'none';
    document.getElementById('notificationForm').reset();
    document.getElementById('specificUserField').style.display = 'none';
}

function toggleSpecificUser() {
    const target = document.getElementById('notificationTarget').value;
    const specificField = document.getElementById('specificUserField');
    specificField.style.display = target === 'specific' ? 'block' : 'none';
}

function sendNotification(event) {
    event.preventDefault();
    
    const target = document.getElementById('notificationTarget').value;
    const title = document.getElementById('notificationTitle').value;
    const message = document.getElementById('notificationMessage').value;
    const type = document.querySelector('input[name="notifType"]:checked').value;
    
    if (!target || !title || !message) {
        alert('Please fill in all required fields');
        return;
    }
    
    if (target === 'specific' && !document.getElementById('specificUser').value) {
        alert('Please select a user');
        return;
    }
    
    alert('Notification sent successfully!');
    closeNotificationModal();
}

function toggleNotifications() {
    const panel = document.getElementById('notificationsPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

// Close notifications when clicking outside
window.onclick = function(event) {
    const modals = ['resolveModal', 'notificationModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    const notificationsPanel = document.getElementById('notificationsPanel');
    const notificationsBtn = document.querySelector('.notifications-btn');
    if (notificationsPanel && notificationsPanel.style.display === 'block' && 
        !notificationsPanel.contains(event.target) && 
        !notificationsBtn.contains(event.target)) {
        notificationsPanel.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide notifications panel after 5 seconds
    setTimeout(() => {
        const panel = document.getElementById('notificationsPanel');
        if (panel) panel.style.display = 'none';
    }, 5000);
});
</script>

<?php include 'footer.php'; ?>