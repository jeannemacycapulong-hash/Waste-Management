<?php
// report-issue.php - Report Issue Page for Villagers
require_once 'config.php';
requireLogin();

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_issue'])) {
    $issue_type = $_POST['issue_type'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $contact = $_POST['contact'] ?? '';
    
    // Validate inputs
    if (empty($issue_type) || empty($location) || empty($description)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // In a real app, you would save to database here
        // For demo, we'll store in session to show success
        $_SESSION['issue_reported'] = [
            'type' => $issue_type,
            'location' => $location,
            'description' => $description,
            'contact' => $contact,
            'date' => date('Y-m-d H:i:s'),
            'status' => 'Pending'
        ];
        $success_message = 'Your issue has been reported successfully!';
    }
}

// Get reported issues from session (demo purposes)
$reported_issues = isset($_SESSION['reported_issues']) ? $_SESSION['reported_issues'] : [];
if (isset($_SESSION['issue_reported'])) {
    array_unshift($reported_issues, $_SESSION['issue_reported']);
    $_SESSION['reported_issues'] = $reported_issues;
    unset($_SESSION['issue_reported']);
}

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