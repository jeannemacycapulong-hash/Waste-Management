<?php
// admin-dashboard.php - Admin Main Dashboard
require_once 'config.php';
requireLogin();

// Check if user is admin
if (getUserRole() !== 'admin') {
    header('Location: role-selection.php');
    exit;
}

// Get statistics from database
$total_villagers = 0;
$total_collectors = 0;
$all_db_users = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT id, username, name, role, is_active FROM users WHERE role IN ('villager','collector') ORDER BY role, name ASC");
    $all_db_users = $stmt->fetchAll();
    foreach ($all_db_users as $u) {
        if ($u['role'] === 'villager') $total_villagers++;
        if ($u['role'] === 'collector') $total_collectors++;
    }
} catch (Exception $e) {
    error_log("Admin dashboard DB error: " . $e->getMessage());
}

// Get pickup statistics
$pickup_stats = getCollectionStats();

// Get all reports
$all_reports = getAllReports();
$pending_reports = count(array_filter($all_reports, fn($r) => $r['status'] === 'pending'));
$resolved_reports = count(array_filter($all_reports, fn($r) => $r['status'] === 'resolved'));

include 'header.php';
?>

<div class="admin-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>Admin Dashboard</h2>
            <p class="welcome-text">Welcome back, <?php echo getUserName(); ?>!</p>
        </div>
        <div class="header-actions">
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
            <a href="#reports" onclick="showTab('reports', event)" class="action-card">
                <i class="fas fa-flag"></i>
                <span>Manage Reports</span>
            </a>
            <a href="#pickups" onclick="showTab('pickups', event)" class="action-card">
                <i class="fas fa-clipboard-check"></i>
                <span>Monitor Pickups</span>
            </a>
            <a href="#notifications" onclick="openNotificationModal(); return false;" class="action-card">
                <i class="fas fa-bell"></i>
                <span>Send Notifications</span>
            </a>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="admin-tabs">
        <button class="tab-btn active" onclick="showTab('reports', event)">Reports Management</button>
        <button class="tab-btn" onclick="showTab('pickups', event)">Pickup Monitoring</button>
        <button class="tab-btn" onclick="showTab('users', event)">User Management</button>
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
                            if (isset($report['reporter_name'])) {
                                echo htmlspecialchars($report['reporter_name']);
                            } elseif (isset($report['name'])) {
                                echo htmlspecialchars($report['name']);
                            } else {
                                echo 'Unknown';
                            }
                            if (isset($report['username'])) {
                                echo ' (' . htmlspecialchars($report['username']) . ')';
                            } elseif (isset($report['reporter'])) {
                                echo ' (' . htmlspecialchars($report['reporter']) . ')';
                            }
                            ?>
                        </p>

                        <p><strong>Type:</strong>
                            <?php echo isset($report['issue_type']) ? str_replace('_', ' ', ucfirst($report['issue_type'])) : 'General'; ?>
                        </p>

                        <p><strong>Location:</strong> <?php echo htmlspecialchars($report['location'] ?? 'N/A'); ?></p>

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
                            <?php echo isset($report['created_at']) ? date('M d, Y h:i A', strtotime($report['created_at'])) : 'Unknown date'; ?>
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
            <?php endforeach; ?>

            <?php if (empty($all_reports)): ?>
                <p class="no-data">No reports found</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pickup Monitoring Tab -->
    <div id="pickups-tab" class="tab-content">
        <?php
        $today_statuses = getTodayCollectionStatuses();
        $collection_stats = getCollectionStats();

        $status_map = [];
        foreach ($today_statuses as $s) {
            $status_map[$s['villager_id']] = $s;
        }

        $all_villagers_pickup = [];
        try {
            $db = getDB();
            $stmt = $db->query("SELECT id, name, address FROM users WHERE role = 'villager' AND is_active = 1 ORDER BY name ASC");
            $all_villagers_pickup = $stmt->fetchAll();
        } catch (Exception $e) {}
        ?>
        <div class="pickup-stats">
            <span class="stat-badge all">Total: <?php echo $collection_stats['total']; ?></span>
            <span class="stat-badge pending">Pending: <?php echo $collection_stats['pending']; ?></span>
            <span class="stat-badge completed">Completed: <?php echo $collection_stats['completed']; ?></span>
            <span class="stat-badge missed">Missed: <?php echo $collection_stats['missed']; ?></span>
            <span class="stat-badge no-waste">No Waste: <?php echo $collection_stats['no_waste']; ?></span>
        </div>

        <div class="pickups-list">
            <p style="color:#888; font-size:0.9rem; margin-bottom:1rem;">
                <i class="fas fa-calendar-day"></i> Showing today's collection status — <?php echo date('F d, Y'); ?>
            </p>
            <?php if (empty($all_villagers_pickup)): ?>
                <p class="no-data">No villagers registered in the system yet.</p>
            <?php else: ?>
            <table class="pickups-table">
                <thead>
                    <tr>
                        <th>Villager</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Collector</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_villagers_pickup as $v):
                        $s = $status_map[$v['id']] ?? null;
                        $st = $s ? $s['status'] : 'pending';
                        $collector_name = $s ? htmlspecialchars($s['collector_name'] ?? '—') : '—';
                        $updated = $s && $s['updated_at'] ? date('h:i A', strtotime($s['updated_at'])) : '—';
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($v['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($v['address'] ?? '—'); ?></td>
                            <td>
                                <span class="status-badge <?php echo $st; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $st)); ?>
                                </span>
                            </td>
                            <td><?php echo $collector_name; ?></td>
                            <td><?php echo $updated; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
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
                        <?php foreach ($all_db_users as $u): ?>
                            <?php if ($u['role'] === 'villager'): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                                    <td><span class="status-badge <?php echo $u['is_active'] ? 'active' : 'inactive'; ?>"><?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
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
                        <?php foreach ($all_db_users as $u): ?>
                            <?php if ($u['role'] === 'collector'): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                                    <td><span class="status-badge <?php echo $u['is_active'] ? 'active' : 'inactive'; ?>"><?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
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
                        <?php foreach ($all_db_users as $u): ?>
                            <option value="<?php echo htmlspecialchars($u['username']); ?>"><?php echo htmlspecialchars($u['name']); ?> (<?php echo $u['role']; ?>)</option>
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

.welcome-text { color: #666; margin: 0.5rem 0 0; }

.header-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.date-display { color: #666; font-weight: 500; }

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

.metric-icon.blue   { background: #e3f2fd; color: #1976d2; }
.metric-icon.green  { background: #e8f5e9; color: #2e7d32; }
.metric-icon.orange { background: #fff3e0; color: #f57c00; }
.metric-icon.purple { background: #f3e5f5; color: #7b1fa2; }

.metric-info { display: flex; flex-direction: column; }

.metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
}

.metric-label { color: #666; font-size: 0.95rem; }

.metric-breakdown {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-size: 0.9rem;
    color: #666;
    padding-top: 0.5rem;
    border-top: 1px solid #e0e0e0;
}

.quick-actions { margin-bottom: 2rem; }

.quick-actions h3 { color: #2e7d32; margin-bottom: 1rem; }

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
    cursor: pointer;
}

.action-card:hover {
    border-color: #8bc34a;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(139, 195, 74, 0.2);
    color: #2e7d32;
}

.action-card i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
}

.action-card span { font-weight: 600; }

.admin-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 0.5rem;
    flex-wrap: wrap;
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
    font-family: 'Poppins', sans-serif;
}

.tab-btn.active { color: #2e7d32; background: #e8f5e9; }

.tab-content { display: none; }
.tab-content.active { display: block; }

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
    font-family: 'Poppins', sans-serif;
}

.reports-filters select:focus { border-color: #8bc34a; }

.report-card {
    background: #f9f9f9;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 5px solid transparent;
}

.report-card.pending  { border-left-color: #f57c00; }
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

.report-type-badge.villager  { background: #e3f2fd; color: #1976d2; }
.report-type-badge.collector { background: #fff3e0; color: #f57c00; }

.report-status {
    padding: 0.3rem 1rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}

.report-status.pending  { background: #fff3e0; color: #f57c00; }
.report-status.resolved { background: #e8f5e9; color: #2e7d32; }

.report-body p { margin: 0.5rem 0; color: #555; }

.urgency-tag {
    padding: 0.2rem 0.8rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.urgency-tag.low    { background: #e8f5e9; color: #2e7d32; }
.urgency-tag.medium { background: #fff3e0; color: #f57c00; }
.urgency-tag.high   { background: #ffebee; color: #f44336; }

.admin-response {
    background: #e8f5e9;
    padding: 1rem;
    border-radius: 10px;
    margin-top: 1rem;
}

.admin-response strong { color: #2e7d32; }

.report-actions { margin-top: 1rem; text-align: right; }

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
    font-family: 'Poppins', sans-serif;
}

.btn-resolve:hover { background: #2e7d32; }

/* Pickup stats badges */
.pickup-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-badge {
    padding: 0.5rem 1.2rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.stat-badge.all       { background: #f5f5f5;  color: #555; }
.stat-badge.pending   { background: #fff3e0;  color: #f57c00; }
.stat-badge.completed { background: #e8f5e9;  color: #2e7d32; }
.stat-badge.missed    { background: #ffebee;  color: #f44336; }
.stat-badge.no-waste  { background: #e3f2fd;  color: #1976d2; }

/* Status badges (table) */
.status-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.82rem;
    font-weight: 600;
}

.status-badge.pending   { background: #fff3e0; color: #f57c00; }
.status-badge.completed { background: #e8f5e9; color: #2e7d32; }
.status-badge.missed    { background: #ffebee; color: #f44336; }
.status-badge.no_waste  { background: #e3f2fd; color: #1976d2; }
.status-badge.active    { background: #e8f5e9; color: #2e7d32; }
.status-badge.inactive  { background: #ffebee; color: #f44336; }

.pickups-table {
    width: 100%;
    border-collapse: collapse;
}

.pickups-table th {
    text-align: left;
    padding: 1rem;
    background: #f5f5f5;
    color: #2e7d32;
    font-weight: 600;
}

.pickups-table td {
    padding: 1rem;
    border-bottom: 1px solid #e0e0e0;
    color: #555;
}

.users-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.user-section h4 { color: #2e7d32; margin-bottom: 1rem; }

.users-table { width: 100%; border-collapse: collapse; }

.users-table th {
    text-align: left;
    padding: 0.8rem;
    background: #f5f5f5;
    font-weight: 600;
    color: #2e7d32;
}

.users-table td {
    padding: 0.8rem;
    border-bottom: 1px solid #e0e0e0;
    color: #555;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    overflow-y: auto;
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
    from { transform: translateY(-50px); opacity: 0; }
    to   { transform: translateY(0);     opacity: 1; }
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 { color: #2e7d32; margin: 0; }

.close {
    font-size: 2rem;
    font-weight: 700;
    color: #999;
    cursor: pointer;
    line-height: 1;
}

.close:hover { color: #333; }

.modal-body { padding: 1.5rem; }

.form-group { margin-bottom: 1.2rem; }

.form-group label {
    display: block;
    font-weight: 600;
    color: #444;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-group input[type="text"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    font-family: 'Poppins', sans-serif;
    outline: none;
    transition: border-color 0.3s;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color: #8bc34a; }

.checkbox-group,
.radio-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.checkbox-group label,
.radio-group label {
    font-weight: 400;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.required { color: #f44336; }

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
    font-family: 'Poppins', sans-serif;
}

.btn-resolve-submit:hover,
.btn-send:hover { background: #2e7d32; }

.no-data {
    text-align: center;
    padding: 3rem;
    color: #999;
    font-style: italic;
}

/* Responsive */
@media (max-width: 992px) {
    .users-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .admin-dashboard { padding: 1.5rem; }
    .metrics-grid { grid-template-columns: 1fr; }
    .action-grid { grid-template-columns: 1fr 1fr; }
    .admin-tabs { flex-direction: column; }
    .reports-filters { flex-direction: column; }
    .report-header { flex-direction: column; align-items: flex-start; }
    .pickups-table th:nth-child(2),
    .pickups-table td:nth-child(2) { display: none; }
}

@media (max-width: 480px) {
    .action-grid { grid-template-columns: 1fr; }
}
</style>

<script>
function showTab(tabName, event) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabName + '-tab').classList.add('active');

    // Find the correct tab button and activate it
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.textContent.toLowerCase().includes(tabName)) {
            btn.classList.add('active');
        }
    });

    if (event) event.preventDefault();
}

function filterReports() {
    const typeFilter   = document.getElementById('reportTypeFilter').value;
    const statusFilter = document.getElementById('reportStatusFilter').value;

    document.querySelectorAll('.report-card').forEach(card => {
        const typeMatch   = typeFilter   === 'all' || card.dataset.type   === typeFilter;
        const statusMatch = statusFilter === 'all' || card.dataset.status === statusFilter;
        card.style.display = typeMatch && statusMatch ? 'block' : 'none';
    });
}

function openResolveModal(reportId) {
    document.getElementById('reportId').value = reportId;
    document.getElementById('resolveModal').style.display = 'block';
}

function closeResolveModal() {
    document.getElementById('resolveModal').style.display = 'none';
    document.getElementById('resolveForm').reset();
}

function resolveIssue(event) {
    event.preventDefault();

    const reportId  = document.getElementById('reportId').value;
    const response  = document.getElementById('responseMessage').value;
    const notifyAll = document.getElementById('notifyAll')?.checked || false;

    if (!response || !reportId) {
        alert('Please enter a response message');
        return;
    }

    const submitBtn   = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const formData = new FormData();
    formData.append('report_id',      reportId);
    formData.append('response',       response);
    formData.append('notify_all',     notifyAll ? '1' : '0');
    formData.append('resolve_report', '1');

    fetch('resolve_report.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeResolveModal();
                showSuccess('Report resolved successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(() => {
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
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
    document.getElementById('specificUserField').style.display = target === 'specific' ? 'block' : 'none';
}

function sendNotification(event) {
    event.preventDefault();

    const target       = document.getElementById('notificationTarget').value;
    const title        = document.getElementById('notificationTitle').value;
    const message      = document.getElementById('notificationMessage').value;
    const type         = document.querySelector('input[name="notifType"]:checked').value;
    const specificUser = document.getElementById('specificUser')?.value || '';

    if (!target || !title || !message) { alert('Please fill in all required fields'); return; }
    if (target === 'specific' && !specificUser) { alert('Please select a user'); return; }

    const submitBtn    = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    const formData = new FormData();
    formData.append('target',       target);
    formData.append('title',        title);
    formData.append('message',      message);
    formData.append('type',         type);
    formData.append('specific_user', specificUser);

    fetch('send_notification.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeNotificationModal();
                showSuccess(data.message);
            } else {
                alert('Error: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(() => {
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
}

function showSuccess(message) {
    const div = document.createElement('div');
    div.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:1rem 2rem;background:#e8f5e9;color:#2e7d32;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.2);font-family:Poppins,sans-serif;font-weight:600;';
    div.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

window.onclick = function(event) {
    ['resolveModal', 'notificationModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (event.target === modal) modal.style.display = 'none';
    });
}
</script>

<?php include 'footer.php'; ?>