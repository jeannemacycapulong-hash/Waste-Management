<?php
// collector-dashboard.php - Collector Main Dashboard
require_once 'config.php';
requireLogin();

// Check if user is collector
if (getUserRole() !== 'collector') {
    header('Location: role-selection.php');
    exit;
}

// Set current date
$current_date = "Thursday, February 05 2026";

// Sample assigned routes (in real app, this would come from database)
$assigned_routes = [
    ['id' => 1, 'village' => 'Baranggay Pampang Purok', 'area' => 'Block 1-5', 'households' => 45],
    ['id' => 2, 'village' => 'Baranggay Pampang Purok', 'area' => 'Block 6-10', 'households' => 38],
    ['id' => 3, 'village' => 'Baranggay Pampang Purok', 'area' => 'Block 11-15', 'households' => 42]
];

include 'header.php';
?>

<div class="dashboard collector-dashboard">
    <div class="dashboard-header">
        <h2>Waste Management App for Collector</h2>
        <div class="collector-badge">
            <i class="fas fa-user-shield"></i>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</span>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <!-- Left Column - Main Tasks -->
        <div class="dashboard-main">
            <!-- Today's Schedule Card -->
            <div class="today-schedule-card">
                <h3><i class="fas fa-calendar-day"></i> Today's Collection Schedule</h3>
                <p class="current-date">
                    <i class="far fa-calendar-alt"></i> <?php echo $current_date; ?>
                </p>
                
                <div class="assigned-route-info">
                    <i class="fas fa-truck"></i>
                    <div class="route-details">
                        <span class="route-label">Assigned Route:</span>
                        <span class="route-name">Baranggay Pampang Purok - Main Route</span>
                    </div>
                </div>
                
                <div class="collection-time-info">
                    <div class="time-badge">
                        <i class="fas fa-clock"></i>
                        <span>Start: 8:30 AM</span>
                    </div>
                    <div class="time-badge">
                        <i class="fas fa-clock"></i>
                        <span>End: 4:30 PM</span>
                    </div>
                </div>
                
                <div class="progress-stats">
                    <div class="stat-item">
                        <span class="stat-value">0/125</span>
                        <span class="stat-label">Completed</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Missed</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">125</span>
                        <span class="stat-label">Total</span>
                    </div>
                </div>
            </div>

            <!-- Villager Checklist Section -->
            <div class="checklist-section">
                <div class="section-header">
                    <h3><i class="fas fa-clipboard-list"></i> Today's Collection Checklist</h3>
                    <div class="filter-options">
                        <select id="statusFilter" class="filter-select" onchange="filterChecklist()">
                            <option value="all">All Households</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="missed">Missed</option>
                            <option value="no_waste">No Waste</option>
                        </select>
                    </div>
                </div>

                <div class="checklist-stats">
                    <span class="stat-badge all">All: 125</span>
                    <span class="stat-badge pending">Pending: 125</span>
                    <span class="stat-badge completed">Completed: 0</span>
                    <span class="stat-badge missed">Missed: 0</span>
                    <span class="stat-badge no-waste">No Waste: 0</span>
                </div>

                <div class="checklist-container" id="checklistContainer">
                    <!-- Sample Villager Entries -->
                    <div class="checklist-item pending" data-status="pending">
                        <div class="item-header">
                            <div class="villager-info">
                                <i class="fas fa-home"></i>
                                <div>
                                    <strong>Juan Dela Cruz</strong>
                                    <span class="address">Blk 1 Lot 2, Pampang Purok</span>
                                </div>
                            </div>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <div class="item-actions">
                            <div class="waste-indicators">
                                <span class="waste-type dry"><i class="fas fa-leaf"></i> Dry</span>
                                <span class="waste-type wet"><i class="fas fa-water"></i> Wet</span>
                            </div>
                            <div class="action-buttons">
                                <button class="btn-status complete" onclick="updateStatus(this, 'completed')">
                                    <i class="fas fa-check"></i> Complete
                                </button>
                                <button class="btn-status missed" onclick="updateStatus(this, 'missed')">
                                    <i class="fas fa-times"></i> Missed
                                </button>
                                <button class="btn-status no-waste" onclick="updateStatus(this, 'no_waste')">
                                    <i class="fas fa-ban"></i> No Waste
                                </button>
                                <button class="btn-report" onclick="openReportModal('Juan Dela Cruz', 'Blk 1 Lot 2, Pampang Purok')">
                                    <i class="fas fa-exclamation-triangle"></i> Report Issue
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="checklist-item pending" data-status="pending">
                        <div class="item-header">
                            <div class="villager-info">
                                <i class="fas fa-home"></i>
                                <div>
                                    <strong>Maria Santos</strong>
                                    <span class="address">Blk 2 Lot 5, Pampang Purok</span>
                                </div>
                            </div>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <div class="item-actions">
                            <div class="waste-indicators">
                                <span class="waste-type dry"><i class="fas fa-leaf"></i> Dry</span>
                                <span class="waste-type wet"><i class="fas fa-water"></i> Wet</span>
                            </div>
                            <div class="action-buttons">
                                <button class="btn-status complete" onclick="updateStatus(this, 'completed')">
                                    <i class="fas fa-check"></i> Complete
                                </button>
                                <button class="btn-status missed" onclick="updateStatus(this, 'missed')">
                                    <i class="fas fa-times"></i> Missed
                                </button>
                                <button class="btn-status no-waste" onclick="updateStatus(this, 'no_waste')">
                                    <i class="fas fa-ban"></i> No Waste
                                </button>
                                <button class="btn-report" onclick="openReportModal('Maria Santos', 'Blk 2 Lot 5, Pampang Purok')">
                                    <i class="fas fa-exclamation-triangle"></i> Report Issue
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="checklist-item pending" data-status="pending">
                        <div class="item-header">
                            <div class="villager-info">
                                <i class="fas fa-home"></i>
                                <div>
                                    <strong>Pedro Reyes</strong>
                                    <span class="address">Blk 3 Lot 8, Pampang Purok</span>
                                </div>
                            </div>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <div class="item-actions">
                            <div class="waste-indicators">
                                <span class="waste-type dry"><i class="fas fa-leaf"></i> Dry</span>
                                <span class="waste-type wet"><i class="fas fa-water"></i> Wet</span>
                            </div>
                            <div class="action-buttons">
                                <button class="btn-status complete" onclick="updateStatus(this, 'completed')">
                                    <i class="fas fa-check"></i> Complete
                                </button>
                                <button class="btn-status missed" onclick="updateStatus(this, 'missed')">
                                    <i class="fas fa-times"></i> Missed
                                </button>
                                <button class="btn-status no-waste" onclick="updateStatus(this, 'no_waste')">
                                    <i class="fas fa-ban"></i> No Waste
                                </button>
                                <button class="btn-report" onclick="openReportModal('Pedro Reyes', 'Blk 3 Lot 8, Pampang Purok')">
                                    <i class="fas fa-exclamation-triangle"></i> Report Issue
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="checklist-item pending" data-status="pending">
                        <div class="item-header">
                            <div class="villager-info">
                                <i class="fas fa-home"></i>
                                <div>
                                    <strong>Ana Lopez</strong>
                                    <span class="address">Blk 4 Lot 12, Pampang Purok</span>
                                </div>
                            </div>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <div class="item-actions">
                            <div class="waste-indicators">
                                <span class="waste-type dry"><i class="fas fa-leaf"></i> Dry</span>
                                <span class="waste-type wet"><i class="fas fa-water"></i> Wet</span>
                            </div>
                            <div class="action-buttons">
                                <button class="btn-status complete" onclick="updateStatus(this, 'completed')">
                                    <i class="fas fa-check"></i> Complete
                                </button>
                                <button class="btn-status missed" onclick="updateStatus(this, 'missed')">
                                    <i class="fas fa-times"></i> Missed
                                </button>
                                <button class="btn-status no-waste" onclick="updateStatus(this, 'no_waste')">
                                    <i class="fas fa-ban"></i> No Waste
                                </button>
                                <button class="btn-report" onclick="openReportModal('Ana Lopez', 'Blk 4 Lot 12, Pampang Purok')">
                                    <i class="fas fa-exclamation-triangle"></i> Report Issue
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="checklist-item pending" data-status="pending">
                        <div class="item-header">
                            <div class="villager-info">
                                <i class="fas fa-home"></i>
                                <div>
                                    <strong>Jose Mercado</strong>
                                    <span class="address">Blk 5 Lot 3, Pampang Purok</span>
                                </div>
                            </div>
                            <span class="status-badge pending">Pending</span>
                        </div>
                        <div class="item-actions">
                            <div class="waste-indicators">
                                <span class="waste-type dry"><i class="fas fa-leaf"></i> Dry</span>
                                <span class="waste-type wet"><i class="fas fa-water"></i> Wet</span>
                            </div>
                            <div class="action-buttons">
                                <button class="btn-status complete" onclick="updateStatus(this, 'completed')">
                                    <i class="fas fa-check"></i> Complete
                                </button>
                                <button class="btn-status missed" onclick="updateStatus(this, 'missed')">
                                    <i class="fas fa-times"></i> Missed
                                </button>
                                <button class="btn-status no-waste" onclick="updateStatus(this, 'no_waste')">
                                    <i class="fas fa-ban"></i> No Waste
                                </button>
                                <button class="btn-report" onclick="openReportModal('Jose Mercado', 'Blk 5 Lot 3, Pampang Purok')">
                                    <i class="fas fa-exclamation-triangle"></i> Report Issue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="load-more">
                    <button class="btn-load-more" onclick="loadMore()">
                        <i class="fas fa-sync-alt"></i> Load More
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Actions & Info -->
        <div class="dashboard-sidebar">
            <!-- Quick Actions -->
            <div class="quick-actions-card">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                <div class="action-grid">
                    <!-- In the Quick Actions section of collector-dashboard.php -->
                    <a href="calendar.php" class="quick-action">
                        <i class="fas fa-calendar-week"></i>
                        <span>Weekly View</span>
                    </a>
                        <!-- other action buttons -->
                    <a href="#" class="quick-action" onclick="openCollectorReportModal(); return false;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Report Issue</span>
                    </a>
                    <!-- <a href="#" class="quick-action" onclick="showRouteMap(); return false;">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>View Route</span>
                    </a> -->
                    <!-- <a href="#" class="quick-action" onclick="generateReport(); return false;">
                        <i class="fas fa-file-alt"></i>
                        <span>Generate Report</span>
                    </a> -->
                </div>
            </div>

            <!-- Assigned Routes -->
            <div class="routes-card">
                <h3><i class="fas fa-route"></i> My Assigned Routes</h3>
                <?php foreach ($assigned_routes as $route): ?>
                <div class="route-item">
                    <div class="route-header">
                        <i class="fas fa-map-pin"></i>
                        <strong><?php echo $route['area']; ?></strong>
                    </div>
                    <div class="route-details">
                        <span class="households"><i class="fas fa-users"></i> <?php echo $route['households']; ?> households</span>
                        <span class="progress">0/<?php echo $route['households']; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

<!-- Report Issue Modal (for specific villager) -->
<div id="reportModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Report Issue at Villager</h3>
            <span class="close" onclick="closeReportModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p><strong>Villager:</strong> <span id="reportVillagerName"></span></p>
            <p><strong>Location:</strong> <span id="reportVillagerLocation"></span></p>
            
            <form id="villagerReportForm" onsubmit="submitVillagerReport(event)">
                <div class="form-group">
                    <label for="issueType">Type of Issue <span class="required">*</span></label>
                    <select id="issueType" required>
                        <option value="">-- Select Issue Type --</option>
                        <option value="no_access">No Access / Gate Locked</option>
                        <option value="aggressive_dog">Aggressive Dog / Pet</option>
                        <option value="uncooperative">Uncooperative Resident</option>
                        <option value="hazardous_waste">Hazardous Waste Found</option>
                        <option value="overweight_bins">Overweight Bins</option>
                        <option value="blocked_access">Blocked Access to Bins</option>
                        <option value="missed_due_to_resident">Missed Due to Resident</option>
                        <option value="damaged_bin">Damaged Bin</option>
                        <option value="improper_sorting">Improper Waste Sorting</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="issueDescription">Description <span class="required">*</span></label>
                    <textarea id="issueDescription" rows="3" required 
                        placeholder="Describe the issue in detail..."></textarea>
                </div>

                <div class="form-group">
                    <label>Action Taken</label>
                    <div class="radio-group">
                        <label><input type="radio" name="actionTaken" value="left_note"> Left Note</label>
                        <label><input type="radio" name="actionTaken" value="spoke_resident"> Spoke with Resident</label>
                        <label><input type="radio" name="actionTaken" value="skipped"> Skipped Collection</label>
                        <label><input type="radio" name="actionTaken" value="called_supervisor"> Called Supervisor</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="issuePhoto">Attach Photo (Optional)</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="issuePhoto" accept="image/*">
                        <label for="issuePhoto" class="file-label">
                            <i class="fas fa-camera"></i> Take/Upload Photo
                        </label>
                    </div>
                </div>

                <div class="form-group urgency-section">
                    <label>Urgency Level</label>
                    <div class="urgency-options">
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="low" checked>
                            <span class="urgency-badge low">Low</span>
                        </label>
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="medium">
                            <span class="urgency-badge medium">Medium</span>
                        </label>
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="high">
                            <span class="urgency-badge high">High</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-submit-report">
                    <i class="fas fa-paper-plane"></i> Submit Report
                </button>
            </form>
        </div>
    </div>
</div>

<!-- General Collector Report Modal -->
<div id="collectorReportModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Report General Issue</h3>
            <span class="close" onclick="closeCollectorReportModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="collectorReportForm" onsubmit="submitCollectorReport(event)">
                <div class="form-group">
                    <label for="genIssueType">Issue Category <span class="required">*</span></label>
                    <select id="genIssueType" required>
                        <option value="">-- Select Category --</option>
                        <option value="vehicle">Vehicle / Truck Issue</option>
                        <option value="equipment">Equipment Problem</option>
                        <option value="route">Route / Navigation Issue</option>
                        <option value="safety">Safety Concern</option>
                        <option value="schedule">Schedule Problem</option>
                        <option value="staff">Staff / Team Issue</option>
                        <option value="public">Public / Traffic Issue</option>
                        <option value="weather">Weather Related</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="genIssueLocation">Location <span class="required">*</span></label>
                    <input type="text" id="genIssueLocation" required placeholder="e.g., Baranggay Pampang Purok, near church">
                </div>

                <div class="form-group">
                    <label for="genIssueDescription">Description <span class="required">*</span></label>
                    <textarea id="genIssueDescription" rows="4" required 
                        placeholder="Describe the issue in detail..."></textarea>
                </div>

                <div class="form-group">
                    <label for="genIssuePhoto">Attach Photo (Optional)</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="genIssuePhoto" accept="image/*">
                        <label for="genIssuePhoto" class="file-label">
                            <i class="fas fa-camera"></i> Take/Upload Photo
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Affected Area</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" value="collection"> Collection Route</label>
                        <label><input type="checkbox" value="schedule"> Schedule</label>
                        <label><input type="checkbox" value="safety"> Safety</label>
                        <label><input type="checkbox" value="equipment"> Equipment</label>
                    </div>
                </div>

                <div class="form-group urgency-section">
                    <label>Urgency Level</label>
                    <div class="urgency-options">
                        <label class="urgency-option">
                            <input type="radio" name="genUrgency" value="low" checked>
                            <span class="urgency-badge low">Low</span>
                        </label>
                        <label class="urgency-option">
                            <input type="radio" name="genUrgency" value="medium">
                            <span class="urgency-badge medium">Medium</span>
                        </label>
                        <label class="urgency-option">
                            <input type="radio" name="genUrgency" value="high">
                            <span class="urgency-badge high">High</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-submit-report">
                    <i class="fas fa-paper-plane"></i> Submit Report
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Route Map Modal -->
<!-- <div id="routeMapModal" class="modal modal-large">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-map-marked-alt"></i> Today's Collection Route</h3>
            <span class="close" onclick="closeRouteMap()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="route-map-container">
                <div class="route-info-panel">
                    <h4>Route Summary</h4>
                    <p><i class="fas fa-road"></i> Total Distance: 5.2 km</p>
                    <p><i class="fas fa-clock"></i> Est. Time: 4 hours</p>
                    <p><i class="fas fa-home"></i> Stops: 125 households</p>
                    
                    <h4>Stops in Order</h4>
                    <ol class="stop-list">
                        <li><strong>Block 1</strong> (Lots 1-25) - 8:30 AM</li>
                        <li><strong>Block 2</strong> (Lots 1-30) - 9:45 AM</li>
                        <li><strong>Block 3</strong> (Lots 1-20) - 11:30 AM</li>
                        <li><strong>Lunch Break</strong> - 12:00 PM - 1:00 PM</li>
                        <li><strong>Block 4</strong> (Lots 1-25) - 1:00 PM</li>
                        <li><strong>Block 5</strong> (Lots 1-25) - 2:30 PM</li>
                        <li><strong>Block 6</strong> (Lots 1-20) - 3:45 PM</li>
                    </ol>
                    
                    <div class="route-notes">
                        <h4><i class="fas fa-exclamation-circle"></i> Notes</h4>
                        <p>• Block 3 has narrow streets - use small truck</p>
                        <p>• Block 5 has aggressive dog at Lot 15</p>
                        <p>• Report any issues immediately</p>
                    </div>
                </div>
                <div class="map-placeholder">
                    <i class="fas fa-map fa-5x"></i>
                    <p>Route map would display here</p>
                    <p class="map-note">(Integration with Google Maps or similar mapping service)</p>
                    <div class="map-controls">
                        <button class="map-btn"><i class="fas fa-zoom-in"></i> Zoom In</button>
                        <button class="map-btn"><i class="fas fa-zoom-out"></i> Zoom Out</button>
                        <button class="map-btn"><i class="fas fa-directions"></i> Get Directions</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- Success Message Modal -->
<div id="successModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header success-header">
            <h3><i class="fas fa-check-circle"></i> Success!</h3>
            <span class="close" onclick="closeSuccessModal()">&times;</span>
        </div>
        <div class="modal-body text-center">
            <i class="fas fa-check-circle fa-4x" style="color: #8bc34a;"></i>
            <p id="successMessage" style="font-size: 1.1rem; margin: 1rem 0;">Action completed successfully!</p>
            <button class="btn-ok" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>
</div>

<style>
/* Additional styles for collector dashboard */
.collector-dashboard {
    background: white;
    border-radius: 30px;
    padding: 2rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

.dashboard-header h2 {
    color: #2e7d32;
    font-size: 2rem;
    margin: 0;
}

.collector-badge {
    background: #8bc34a;
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-weight: 600;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

/* Today's Schedule Card */
.today-schedule-card {
    background: linear-gradient(135deg, #8bc34a, #6a994e);
    color: white;
    border-radius: 20px;
    padding: 1.8rem;
    margin-bottom: 2rem;
}

.today-schedule-card h3 {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.current-date {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
}

.assigned-route-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: rgba(255,255,255,0.2);
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}

.route-details {
    display: flex;
    flex-direction: column;
}

.route-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.route-name {
    font-weight: 600;
    font-size: 1.2rem;
}

.collection-time-info {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.time-badge {
    background: rgba(255,255,255,0.2);
    padding: 0.5rem 1rem;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.progress-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 2rem;
    font-weight: 700;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Checklist Section */
.checklist-section {
    background: #f9f9f9;
    border-radius: 20px;
    padding: 1.5rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.section-header h3 {
    color: #2e7d32;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    outline: none;
    cursor: pointer;
}

.filter-select:focus {
    border-color: #8bc34a;
}

.checklist-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.stat-badge.all { background: #e0e0e0; color: #333; }
.stat-badge.pending { background: #fff3e0; color: #f57c00; }
.stat-badge.completed { background: #e8f5e9; color: #2e7d32; }
.stat-badge.missed { background: #ffebee; color: #c62828; }
.stat-badge.no-waste { background: #e1f5fe; color: #0288d1; }

.checklist-item {
    background: white;
    border-radius: 15px;
    padding: 1.2rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-left: 5px solid transparent;
    transition: all 0.3s;
}

.checklist-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.checklist-item[data-status="pending"] { border-left-color: #f57c00; }
.checklist-item[data-status="completed"] { border-left-color: #2e7d32; }
.checklist-item[data-status="missed"] { border-left-color: #c62828; }
.checklist-item[data-status="no_waste"] { border-left-color: #0288d1; }

.item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.villager-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.villager-info i {
    font-size: 1.5rem;
    color: #8bc34a;
}

.villager-info strong {
    display: block;
    font-size: 1.1rem;
    color: #333;
}

.address {
    font-size: 0.9rem;
    color: #666;
}

.status-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-badge.pending { background: #fff3e0; color: #f57c00; }
.status-badge.completed { background: #e8f5e9; color: #2e7d32; }
.status-badge.missed { background: #ffebee; color: #c62828; }
.status-badge.no-waste { background: #e1f5fe; color: #0288d1; }

.item-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.waste-indicators {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.waste-type {
    padding: 0.3rem 0.8rem;
    border-radius: 5px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.waste-type.dry { background: #e8f5e9; color: #2e7d32; }
.waste-type.wet { background: #e3f2fd; color: #1976d2; }

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-status {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 5px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: all 0.3s;
}

.btn-status.complete { background: #e8f5e9; color: #2e7d32; }
.btn-status.missed { background: #ffebee; color: #c62828; }
.btn-status.no-waste { background: #e1f5fe; color: #0288d1; }

.btn-status:hover {
    transform: translateY(-2px);
    filter: brightness(0.95);
}

.btn-report {
    padding: 0.5rem 1rem;
    background: #fff3e0;
    color: #f57c00;
    border: none;
    border-radius: 5px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: all 0.3s;
}

.btn-report:hover {
    background: #f57c00;
    color: white;
}

.completion-time {
    color: #2e7d32;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.load-more {
    text-align: center;
    margin-top: 1.5rem;
}

.btn-load-more {
    padding: 0.8rem 2rem;
    background: white;
    border: 2px solid #8bc34a;
    color: #2e7d32;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-load-more:hover {
    background: #8bc34a;
    color: white;
}

/* Sidebar Cards */
.quick-actions-card,
.routes-card,
.reports-card,
.stats-card,
.notes-card {
    background: #f9f9f9;
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.quick-actions-card h3,
.routes-card h3,
.reports-card h3,
.stats-card h3,
.notes-card h3 {
    color: #2e7d32;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.2rem;
    font-size: 1.3rem;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.quick-action {
    background: white;
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    border: 2px solid transparent;
}

.quick-action:hover {
    border-color: #8bc34a;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(139, 195, 74, 0.2);
}

.quick-action i {
    font-size: 1.8rem;
    color: #8bc34a;
}

.quick-action span {
    font-size: 0.9rem;
    font-weight: 600;
}

.route-item {
    background: white;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.route-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.route-header i {
    color: #8bc34a;
}

.route-details {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    color: #666;
}

.report-item {
    display: flex;
    gap: 1rem;
    padding: 0.8rem 0;
    border-bottom: 1px solid #e0e0e0;
}

.report-item:last-child {
    border-bottom: none;
}

.report-details {
    flex: 1;
}

.report-title {
    display: block;
    font-weight: 600;
    color: #333;
}

.report-location {
    font-size: 0.85rem;
    color: #666;
}

.report-time {
    font-size: 0.8rem;
    color: #999;
}

.view-all-link {
    display: inline-block;
    margin-top: 1rem;
    color: #8bc34a;
    text-decoration: none;
    font-weight: 600;
}

.view-all-link:hover {
    text-decoration: underline;
}

.stats-card .stat-item {
    display: flex;
    justify-content: space-between;
    padding: 0.8rem 0;
    border-bottom: 1px dashed #e0e0e0;
}

.stats-card .stat-item:last-child {
    border-bottom: none;
}

.stat-name {
    color: #555;
}

.stat-value {
    font-weight: 600;
    color: #2e7d32;
}

.quick-notes {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-family: inherit;
    resize: vertical;
    margin-bottom: 1rem;
}

.quick-notes:focus {
    border-color: #8bc34a;
    outline: none;
}

.btn-save-notes {
    width: 100%;
    padding: 0.8rem;
    background: #8bc34a;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-save-notes:hover {
    background: #2e7d32;
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
    overflow-y: auto;
}

.modal-content {
    background: white;
    margin: 50px auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 30px 60px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease-out;
}

.modal-small {
    max-width: 400px;
}

.modal-large .modal-content {
    max-width: 1000px;
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
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.success-header {
    background: #e8f5e9;
}

.success-header h3 {
    color: #2e7d32;
}

.close {
    font-size: 2rem;
    font-weight: 700;
    color: #999;
    cursor: pointer;
    transition: color 0.3s;
    line-height: 1;
}

.close:hover {
    color: #333;
}

.modal-body {
    padding: 1.5rem;
}

.text-center {
    text-align: center;
}

.btn-ok {
    padding: 0.8rem 2rem;
    background: #8bc34a;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-ok:hover {
    background: #2e7d32;
}

/* Route Map */
.route-map-container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
}

.route-info-panel {
    background: #f9f9f9;
    padding: 1.5rem;
    border-radius: 15px;
}

.route-info-panel h4 {
    color: #2e7d32;
    margin: 1rem 0 0.5rem;
}

.route-info-panel h4:first-child {
    margin-top: 0;
}

.stop-list {
    padding-left: 1.5rem;
    margin: 0;
}

.stop-list li {
    margin: 0.5rem 0;
    color: #555;
}

.route-notes {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e0e0e0;
}

.route-notes p {
    margin: 0.3rem 0;
    font-size: 0.9rem;
    color: #666;
}

.map-placeholder {
    background: #f5f5f5;
    border-radius: 15px;
    padding: 3rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.map-placeholder i {
    color: #8bc34a;
    opacity: 0.5;
}

.map-note {
    font-size: 0.9rem;
    color: #999;
    font-style: italic;
}

.map-controls {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.map-btn {
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #8bc34a;
    color: #2e7d32;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.map-btn:hover {
    background: #8bc34a;
    color: white;
}

/* Radio and Checkbox groups */
.radio-group,
.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 0.5rem;
}

.radio-group label,
.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    cursor: pointer;
    font-size: 0.95rem;
    color: #555;
}

.radio-group input[type="radio"],
.checkbox-group input[type="checkbox"] {
    cursor: pointer;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 992px) {
    .route-map-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .collector-dashboard {
        padding: 1.5rem;
    }
    
    .dashboard-header h2 {
        font-size: 1.5rem;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .item-actions {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .action-buttons {
        width: 100%;
        justify-content: flex-start;
    }
    
    .modal-content {
        margin: 20px auto;
        width: 95%;
    }
}

@media (max-width: 480px) {
    .collector-dashboard {
        padding: 1rem;
    }
    
    .progress-stats {
        grid-template-columns: 1fr;
    }
    
    .checklist-stats {
        flex-direction: column;
    }
    
    .villager-info {
        flex-wrap: wrap;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-status, .btn-report {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Filter checklist items
function filterChecklist() {
    const filter = document.getElementById('statusFilter').value;
    const items = document.querySelectorAll('.checklist-item');
    
    items.forEach(item => {
        if (filter === 'all' || item.dataset.status === filter) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Update stats count
    updateStats();
}

// Update status
function updateStatus(button, status) {
    const checklistItem = button.closest('.checklist-item');
    const statusBadge = checklistItem.querySelector('.status-badge');
    const actionButtons = checklistItem.querySelector('.action-buttons');
    
    // Update data attribute
    checklistItem.dataset.status = status;
    
    // Update status badge
    statusBadge.className = 'status-badge ' + status;
    statusBadge.textContent = status.split('_').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
    
    // Replace action buttons with completion time
    const completionTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    actionButtons.innerHTML = `<span class="completion-time"><i class="far fa-clock"></i> ${completionTime}</span>`;
    
    // Update stats
    updateStats();
    
    // Show success message
    showSuccess('Status updated successfully!');
}

// Update statistics
function updateStats() {
    const items = document.querySelectorAll('.checklist-item');
    let pending = 0, completed = 0, missed = 0, noWaste = 0;
    
    items.forEach(item => {
        switch(item.dataset.status) {
            case 'pending': pending++; break;
            case 'completed': completed++; break;
            case 'missed': missed++; break;
            case 'no_waste': noWaste++; break;
        }
    });
    
    // Update stat badges
    const total = items.length;
    document.querySelector('.stat-badge.all').textContent = `All: ${total}`;
    document.querySelector('.stat-badge.pending').textContent = `Pending: ${pending}`;
    document.querySelector('.stat-badge.completed').textContent = `Completed: ${completed}`;
    document.querySelector('.stat-badge.missed').textContent = `Missed: ${missed}`;
    document.querySelector('.stat-badge.no-waste').textContent = `No Waste: ${noWaste}`;
    
    // Update progress stats
    document.querySelector('.progress-stats .stat-value').textContent = `${completed}/${total}`;
}

// Load more items (demo)
function loadMore() {
    alert('Loading more villagers... (Demo - would load from database)');
}

// Report Modal Functions
function openReportModal(villagerName, location) {
    document.getElementById('reportVillagerName').textContent = villagerName;
    document.getElementById('reportVillagerLocation').textContent = location;
    document.getElementById('reportModal').style.display = 'block';
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
    document.getElementById('villagerReportForm').reset();
}

function openCollectorReportModal() {
    document.getElementById('collectorReportModal').style.display = 'block';
}

function closeCollectorReportModal() {
    document.getElementById('collectorReportModal').style.display = 'none';
    document.getElementById('collectorReportForm').reset();
}

// Submit villager report (for reporting issues about a specific villager)
function submitVillagerReport(event) {
    event.preventDefault();
    
    // Get form values
    const issueType = document.getElementById('issueType').value;
    const description = document.getElementById('issueDescription').value;
    const actionTaken = document.querySelector('input[name="actionTaken"]:checked')?.value || '';
    const urgency = document.querySelector('input[name="urgency"]:checked')?.value || 'low';
    const villagerName = document.getElementById('reportVillagerName').textContent;
    const location = document.getElementById('reportVillagerLocation').textContent;
    
    if (!issueType || !description) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    // Create form data to send to server
    const formData = new FormData();
    formData.append('reporter_type', 'collector');
    formData.append('issue_type', issueType);
    formData.append('location', location + ' - ' + villagerName);
    formData.append('description', description + ' (Action taken: ' + actionTaken + ')');
    formData.append('urgency', urgency);
    formData.append('contact', '');
    formData.append('submit_report', '1');
    
    // Send AJAX request
    fetch('submit_report.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeReportModal();
            showSuccess('Issue reported to admin successfully!');
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

// Submit collector general report
function submitCollectorReport(event) {
    event.preventDefault();
    
    // Get form values
    const issueType = document.getElementById('genIssueType').value;
    const location = document.getElementById('genIssueLocation').value;
    const description = document.getElementById('genIssueDescription').value;
    const urgency = document.querySelector('input[name="genUrgency"]:checked')?.value || 'low';
    
    // Get checked affected areas
    const affectedAreas = [];
    document.querySelectorAll('#collectorReportForm input[type="checkbox"]:checked').forEach(cb => {
        affectedAreas.push(cb.value);
    });
    
    if (!issueType || !location || !description) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    // Create form data
    const formData = new FormData();
    formData.append('reporter_type', 'collector');
    formData.append('issue_type', issueType);
    formData.append('location', location);
    formData.append('description', description + ' (Affected areas: ' + affectedAreas.join(', ') + ')');
    formData.append('urgency', urgency);
    formData.append('contact', '');
    formData.append('submit_report', '1');
    
    // Send AJAX request
    fetch('submit_report.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCollectorReportModal();
            showSuccess('General issue reported to admin successfully!');
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

// Route Map Functions
function showRouteMap() {
    document.getElementById('routeMapModal').style.display = 'block';
}

function closeRouteMap() {
    document.getElementById('routeMapModal').style.display = 'none';
}

// Success Modal Functions
function showSuccess(message) {
    document.getElementById('successMessage').textContent = message;
    document.getElementById('successModal').style.display = 'block';
}

function closeSuccessModal() {
    document.getElementById('successModal').style.display = 'none';
}

// Save Notes
function saveNotes() {
    const notes = document.querySelector('.quick-notes').value;
    if (notes.trim()) {
        showSuccess('Notes saved successfully!');
    } else {
        alert('Please enter some notes first');
    }
}

// Generate Report
function generateReport() {
    showSuccess('Report generated successfully! (Demo)');
}

// View All Reports
function viewAllReports() {
    showSuccess('Viewing all reports (Demo)');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = 'none';
        }
    }
}

// File input display
document.addEventListener('DOMContentLoaded', function() {
    const photoInputs = document.querySelectorAll('input[type="file"]');
    photoInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const label = this.nextElementSibling;
                if (label && label.classList.contains('file-label')) {
                    label.innerHTML = '<i class="fas fa-check"></i> ' + fileName;
                }
            }
        });
    });
    
    // Initialize stats
    updateStats();
});
</script>

<?php include 'footer.php'; ?>