<?php
// monthly-dues.php - Monthly Dues Page
require_once 'config.php';
requireLogin();

// Check if user is villager
if (getUserRole() !== 'villager') {
    header('Location: role-selection.php');
    exit;
}

// Handle pay now action
if (isset($_POST['pay_now'])) {
    $_SESSION['payment_message'] = 'Payment initiated successfully! (Demo)';
}

include 'header.php';
?>

<div class="dues-container">
    <h2>Waste Management App for Villager</h2>
    <h3><i class="fas fa-file-invoice-dollar"></i> Monthly Due</h3>

    <?php if (isset($_SESSION['payment_message'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> 
            <?php 
            echo $_SESSION['payment_message']; 
            unset($_SESSION['payment_message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="payment-due-card">
        <div class="due-info">
            <p><i class="far fa-calendar-alt"></i> 
                Next Payment Due: <strong>February 1, 2026</strong>
            </p>
            <p class="amount">Amount: <strong>₱ 1,000</strong></p>
        </div>
        <form method="POST" action="" class="pay-now-form">
            <button type="submit" name="pay_now" class="btn-pay-now">
                <i class="fas fa-credit-card"></i> PAY NOW
            </button>
        </form>
    </div>

    <div class="payment-history">
        <h4>Payment History</h4>
        <table class="history-table">
            <tr>
                <td>January 15, 2026</td>
                <td class="status-badge not-paid">NOT PAID</td>
            </tr>
            <tr>
                <td>December 10, 2025</td>
                <td class="status-badge paid">PAID</td>
            </tr>
            <tr>
                <td>November 10, 2025</td>
                <td class="status-badge paid">PAID</td>
            </tr>
            <tr>
                <td>November 10, 2024</td>
                <td class="status-badge paid">PAID</td>
            </tr>
        </table>
    </div>

    <div class="extra-sections">
        <div class="placeholder-section">
            <i class="fas fa-hand-holding-heart"></i>
            <h5>Monthly Compensates</h5>
            <p>₱ 0.00 (demo placeholder)</p>
        </div>
        <div class="placeholder-section">
            <i class="fas fa-credit-card"></i>
            <h5>Payment Method</h5>
            <p>Cash / GCash / Card (demo)</p>
        </div>
    </div>

    <div class="back-link">
        <a href="villager-dashboard.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>