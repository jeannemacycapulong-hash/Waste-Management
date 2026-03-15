<?php
// report-issue.php - Report Issue Page for Villagers
require_once 'config.php';
requireLogin();

// Check if user is villager
if (getUserRole() !== 'villager') {
    header('Location: role-selection.php');
    exit;
}

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_issue'])) {
    $issue_type = $_POST['issue_type'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $urgency = $_POST['urgency'] ?? 'low';
    
    // Validate inputs
    if (empty($issue_type) || empty($location) || empty($description)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Add report to system using the new function
        addReport('villager', [
            'issue_type' => $issue_type,
            'location' => $location,
            'description' => $description,
            'contact' => $contact,
            'urgency' => $urgency
        ]);
        
        $success_message = 'Your issue has been reported successfully! Admin has been notified.';
    }
}

// Get reported issues from session (demo purposes)
$reported_issues = isset($_SESSION['reported_issues']) ? $_SESSION['reported_issues'] : [];

include 'header.php';
?>

<div class="report-issue-container">
    <div class="report-header">
        <h2><i class="fas fa-exclamation-triangle"></i> Report an Issue</h2>
        <p class="subtitle">Help us improve our waste collection service by reporting any problems you encounter.</p>
        <a href="villager-dashboard.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if ($success_message): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> 
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> 
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="report-content">
        <!-- Issue Form -->
        <div class="report-form-section">
            <h3><i class="fas fa-pen"></i> Submit New Issue</h3>
            
            <form method="POST" action="" class="report-form" id="reportForm">
                <div class="form-group">
                    <label for="issue_type">Type of Issue <span class="required">*</span></label>
                    <select name="issue_type" id="issue_type" required>
                        <option value="">-- Select Issue Type --</option>
                        <option value="missed_collection">Missed Collection</option>
                        <option value="late_collection">Late Collection</option>
                        <option value="spilled_garbage">Spilled Garbage</option>
                        <option value="uncollected_waste">Uncollected Waste</option>
                        <option value="broken_bin">Broken/Damaged Bin</option>
                        <option value="collector_issue">Collector Behavior Issue</option>
                        <option value="schedule_change">Schedule Change Request</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location">Location/Address <span class="required">*</span></label>
                    <input type="text" name="location" id="location" 
                           placeholder="e.g., Baranggay Pampang Purok, Angeles City" required>
                    <small class="form-hint">Provide specific location details</small>
                </div>

                <div class="form-group">
                    <label for="description">Description of Issue <span class="required">*</span></label>
                    <textarea name="description" id="description" rows="4" 
                              placeholder="Please describe the issue in detail..." required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="contact">Contact Number</label>
                        <input type="tel" name="contact" id="contact" 
                               placeholder="e.g., 09123456789">
                        <small class="form-hint">For follow-up if needed</small>
                    </div>

                    <div class="form-group half">
                        <label for="photo">Attach Photo (Optional)</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="photo" id="photo" accept="image/*">
                            <label for="photo" class="file-label">
                                <i class="fas fa-camera"></i> Choose Photo
                            </label>
                        </div>
                        <small class="form-hint">Max size: 5MB</small>
                    </div>
                </div>

                <div class="form-group urgency-section">
                    <label>Urgency Level</label>
                    <div class="urgency-options">
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="low" checked>
                            <span class="urgency-badge low">
                                <i class="fas fa-thermometer-quarter"></i> Low
                            </span>
                        </label>
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="medium">
                            <span class="urgency-badge medium">
                                <i class="fas fa-thermometer-half"></i> Medium
                            </span>
                        </label>
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="high">
                            <span class="urgency-badge high">
                                <i class="fas fa-thermometer-full"></i> High
                            </span>
                        </label>
                    </div>
                </div>

                <div class="form-group agreement">
                    <label class="checkbox-label">
                        <input type="checkbox" name="agree" required>
                        <span>I confirm that the information provided is accurate <span class="required">*</span></span>
                    </label>
                </div>

                <button type="submit" name="submit_issue" class="btn-submit-report">
                    <i class="fas fa-paper-plane"></i> Submit Report
                </button>
            </form>
        </div>

        <!-- Quick Tips & My Reports -->
        <div class="report-sidebar">
            <!-- Quick Tips -->
            <div class="quick-tips">
                <h4><i class="fas fa-lightbulb"></i> Quick Tips</h4>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Be specific about the location</li>
                    <li><i class="fas fa-check-circle"></i> Include photos if possible</li>
                    <li><i class="fas fa-check-circle"></i> Provide accurate contact details</li>
                    <li><i class="fas fa-check-circle"></i> Check if issue is already reported</li>
                    <li><i class="fas fa-check-circle"></i> Emergency? Call us: (045) 123-4567</li>
                </ul>
            </div>

            <!-- Common Issues FAQ -->
            <div class="faq-section">
                <h4><i class="fas fa-question-circle"></i> Common Issues</h4>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-chevron-right"></i>
                        <span>Missed collection?</span>
                    </div>
                    <div class="faq-answer">
                        Report it here and we'll schedule a special collection within 24 hours.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-chevron-right"></i>
                        <span>Spilled garbage?</span>
                    </div>
                    <div class="faq-answer">
                        Our team will clean it up within 12 hours. Please provide exact location.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-chevron-right"></i>
                        <span>Change collection day?</span>
                    </div>
                    <div class="faq-answer">
                        Schedule changes require 48 hours notice. Use this form to request.
                    </div>
                </div>
            </div>

            <!-- My Recent Reports (Demo) -->
            <?php if (!empty($reported_issues)): ?>
            <div class="recent-reports">
                <h4><i class="fas fa-history"></i> My Recent Reports</h4>
                <?php foreach (array_slice($reported_issues, 0, 3) as $issue): ?>
                <div class="report-card">
                    <div class="report-card-header">
                        <span class="issue-type"><?php echo ucfirst(str_replace('_', ' ', $issue['type'])); ?></span>
                        <span class="issue-status pending"><?php echo $issue['status']; ?></span>
                    </div>
                    <div class="report-card-body">
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($issue['location']); ?></p>
                        <p class="issue-description"><?php echo htmlspecialchars(substr($issue['description'], 0, 50)) . '...'; ?></p>
                        <small><i class="far fa-clock"></i> <?php echo date('M d, Y', strtotime($issue['date'])); ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleFAQ(element) {
    element.classList.toggle('active');
    const answer = element.nextElementSibling;
    answer.classList.toggle('show');
}

// File input display
document.getElementById('photo').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        const label = document.querySelector('.file-label');
        label.innerHTML = '<i class="fas fa-check"></i> ' + fileName;
    }
});

// Form validation
document.getElementById('reportForm').addEventListener('submit', function(e) {
    const issueType = document.getElementById('issue_type').value;
    const location = document.getElementById('location').value;
    const description = document.getElementById('description').value;
    const agree = document.querySelector('input[name="agree"]').checked;
    
    if (!issueType || !location || !description || !agree) {
        e.preventDefault();
        alert('Please fill in all required fields and agree to the terms.');
    }
});
</script>

<?php include 'footer.php'; ?>
<style>
    /* Report Issue Page Styles */
.report-issue-container {
    background: white;
    border-radius: 30px;
    padding: 2rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
}

.report-header h2 {
    color: #2e7d32;
    margin: 0;
    font-size: 2rem;
}

.report-header h2 i {
    color: #f44336;
    margin-right: 0.5rem;
}

.report-header .subtitle {
    color: #666;
    margin: 0.5rem 0 0;
    font-size: 1rem;
    flex-basis: 100%;
}

.report-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-top: 1rem;
}

/* Form Section */
.report-form-section {
    background: #f9f9f9;
    padding: 2rem;
    border-radius: 20px;
}

.report-form-section h3 {
    color: #2e7d32;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.report-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
}

.form-group .required {
    color: #f44336;
}

.form-group input[type="text"],
.form-group input[type="tel"],
.form-group select,
.form-group textarea {
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #8bc34a;
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 195, 74, 0.1);
}

.form-hint {
    font-size: 0.85rem;
    color: #888;
    margin-top: 0.3rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
/* File Input */
.file-input-wrapper {
    position: relative;
}

.file-input-wrapper input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 0.1px;
    height: 0.1px;
}

.file-label {
    display: inline-block;
    padding: 1rem 2rem;
    background: #e8f5e9;
    color: #2e7d32;
    border: 2px dashed #8bc34a;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    width: 100%;
    text-align: center;
}

.file-label:hover {
    background: #8bc34a;
    color: white;
    border-color: #2e7d32;
}

.file-label i {
    margin-right: 0.5rem;
}
/* Urgency Section */
.urgency-section {
    border-top: 1px solid #e0e0e0;
    padding-top: 1.5rem;
}

.urgency-options {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}

.urgency-option {
    flex: 1;
    min-width: 100px;
}

.urgency-option input[type="radio"] {
    display: none;
}

.urgency-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s;
    text-align: center;
}

.urgency-badge.low {
    background: #e8f5e9;
    color: #2e7d32;
}

.urgency-badge.medium {
    background: #fff3e0;
    color: #f57c00;
}

.urgency-badge.high {
    background: #ffebee;
    color: #f44336;
}

.urgency-option input[type="radio"]:checked + .urgency-badge {
    border-color: currentColor;
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
/* Agreement Checkbox */
.agreement {
    margin: 0.5rem 0;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.checkbox-label span {
    font-size: 0.95rem;
    color: #555;
}

/* Submit Button */
.btn-submit-report {
    background: #8bc34a;
    color: white;
    border: none;
    padding: 1.2rem;
    border-radius: 10px;
    font-size: 1.2rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-submit-report:hover {
    background: #2e7d32;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(46, 125, 50, 0.3);
}
/* Sidebar */
.report-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.quick-tips,
.faq-section,
.recent-reports {
    background: #f9f9f9;
    padding: 1.5rem;
    border-radius: 20px;
}

.quick-tips h4,
.faq-section h4,
.recent-reports h4 {
    color: #2e7d32;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.2rem;
}

.quick-tips ul {
    list-style: none;
    padding: 0;
}

.quick-tips li {
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    color: #555;
    border-bottom: 1px dashed #e0e0e0;
}

.quick-tips li:last-child {
    border-bottom: none;
}

.quick-tips li i {
    color: #8bc34a;
}

/* FAQ Section */
.faq-item {
    margin-bottom: 0.5rem;
}

.faq-question {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    color: #333;
    transition: all 0.3s;
}

.faq-question:hover {
    background: #e8f5e9;
}

.faq-question i {
    color: #8bc34a;
    transition: transform 0.3s;
}

.faq-question.active i {
    transform: rotate(90deg);
}

.faq-answer {
    padding: 0 0.8rem 0.8rem 2.2rem;
    font-size: 0.95rem;
    color: #666;
    line-height: 1.5;
    display: none;
}

.faq-answer.show {
    display: block;
}

/* Recent Reports */
.recent-reports {
    max-height: 400px;
    overflow-y: auto;
}

.report-card {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #8bc34a;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.report-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8rem;
}

.issue-type {
    font-weight: 600;
    color: #2e7d32;
    font-size: 0.9rem;
}

.issue-status {
    font-size: 0.8rem;
    padding: 0.2rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
}

.issue-status.pending {
    background: #fff3e0;
    color: #f57c00;
}

.issue-status.resolved {
    background: #e8f5e9;
    color: #2e7d32;
}

.issue-status.in-progress {
    background: #e3f2fd;
    color: #1976d2;
}

.report-card-body p {
    margin: 0.3rem 0;
    color: #555;
    font-size: 0.9rem;
}

.report-card-body i {
    color: #8bc34a;
    width: 20px;
}

.issue-description {
    color: #666;
    font-style: italic;
}

.report-card-body small {
    color: #888;
    display: block;
    margin-top: 0.5rem;
}
/* Responsive Design */
@media (max-width: 992px) {
    .report-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .report-issue-container {
        padding: 1.5rem;
    }
    
    .report-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .urgency-options {
        flex-direction: column;
    }
    
    .urgency-option {
        min-width: auto;
    }
}

@media (max-width: 480px) {
    .report-issue-container {
        padding: 1rem;
    }
    
    .report-form-section {
        padding: 1rem;
    }
}
</style>