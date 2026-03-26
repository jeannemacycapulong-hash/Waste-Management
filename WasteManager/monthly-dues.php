<?php
// monthly-dues.php - Monthly Dues Page
require_once 'config.php';
requireLogin();

if (getUserRole() !== 'villager') {
    header('Location: role-selection.php');
    exit;
}

$user_id = getUserId();

// Handle PAY NOW
if (isset($_POST['pay_now'])) {
    $current_due = getOrCreateCurrentDue($user_id);
    if ($current_due && $current_due['status'] === 'unpaid') {
        $method = $_POST['payment_method'] ?? 'cash';
        $result = payMonthlyDue($current_due['id'], $method);

        if ($result) {
            addNotification($user_id, 'Payment Received', 'Your monthly due for ' . date('F Y', strtotime($current_due['due_month'])) . ' has been marked as paid. Thank you!', 'success');

            try {
                $db = getDB();
                $admins = $db->query("SELECT id FROM users WHERE role = 'admin'")->fetchAll();
                $name = getUserName();
                foreach ($admins as $admin) {
                    addNotification($admin['id'], 'Payment Received', "$name has paid their monthly due for " . date('F Y', strtotime($current_due['due_month'])) . ".", 'info');
                }
            } catch (Exception $e) { error_log($e->getMessage()); }

            $_SESSION['payment_message'] = 'Payment successful! Your due for ' . date('F Y', strtotime($current_due['due_month'])) . ' is now marked as PAID.';
            header('Location: monthly-dues.php');
            exit;
        } else {
            $error = 'Payment failed. Please try again.';
        }
    }
}

$current_due = getOrCreateCurrentDue($user_id);
$all_dues    = getVillagerDues($user_id);

// Fallback: manually build current due if function returns nothing
if (!$current_due) {
    try {
        $db = getDB();
        $this_month = date('Y-m-01');

        // Try to get existing
        $stmt = $db->prepare("SELECT * FROM monthly_dues WHERE villager_id = :id AND due_month = :month");
        $stmt->execute([':id' => $user_id, ':month' => $this_month]);
        $current_due = $stmt->fetch();

        // If still nothing, insert one
        if (!$current_due) {
            $ins = $db->prepare("INSERT INTO monthly_dues (villager_id, due_month, amount, status) VALUES (:id, :month, 1000.00, 'unpaid')");
            $ins->execute([':id' => $user_id, ':month' => $this_month]);
            $new_id = $db->lastInsertId();
            $stmt2  = $db->prepare("SELECT * FROM monthly_dues WHERE id = :id");
            $stmt2->execute([':id' => $new_id]);
            $current_due = $stmt2->fetch();
        }
    } catch (Exception $e) {
        error_log("monthly-dues fallback error: " . $e->getMessage());
    }
}

// Fallback for all dues
if (empty($all_dues)) {
    try {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM monthly_dues WHERE villager_id = :id ORDER BY due_month DESC");
        $stmt->execute([':id' => $user_id]);
        $all_dues = $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("monthly-dues all_dues fallback: " . $e->getMessage());
    }
}

include 'header.php';
?>

<div class="dues-container">
    <h2>Waste Management App for Villager</h2>
    <h3><i class="fas fa-file-invoice-dollar"></i> Monthly Due</h3>

    <?php if (isset($_SESSION['payment_message'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <?php echo $_SESSION['payment_message']; unset($_SESSION['payment_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($current_due): ?>
    <div class="payment-due-card <?php echo $current_due['status'] === 'paid' ? 'is-paid' : ''; ?>">
        <div class="due-info">
            <?php if ($current_due['status'] === 'paid'): ?>
                <p class="due-label"><i class="far fa-calendar-alt"></i> Paid for: <strong><?php echo date('F Y', strtotime($current_due['due_month'])); ?></strong></p>
                <p class="amount">Amount: <strong>₱ <?php echo number_format($current_due['amount'], 2); ?></strong></p>
                <p><i class="fas fa-check-circle"></i> Paid on <?php echo date('F d, Y', strtotime($current_due['paid_at'])); ?> via <?php echo ucfirst($current_due['payment_method']); ?></p>
            <?php else: ?>
                <p class="due-label"><i class="far fa-calendar-alt"></i> Next Payment Due: <strong><?php echo date('F j, Y', strtotime($current_due['due_month'] . ' +1 month -1 day')); ?></strong></p>
                <p class="amount">Amount: <strong>₱ <?php echo number_format($current_due['amount'], 2); ?></strong></p>
            <?php endif; ?>
        </div>

        <?php if ($current_due['status'] === 'unpaid'): ?>
        <form method="POST" action="" class="pay-now-form">
            <div class="payment-method-select">
                <label>Payment Method</label>
                <select name="payment_method" class="method-select">
                    <option value="cash">Cash</option>
                    <option value="gcash">GCash</option>
                    <option value="card">Card</option>
                </select>
            </div>
            <button type="submit" name="pay_now" class="btn-pay-now">
                <i class="fas fa-credit-card"></i> PAY NOW
            </button>
        </form>
        <?php else: ?>
            <div class="paid-badge"><i class="fas fa-check-circle"></i> PAID</div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- Fallback card if current_due is still null -->
    <div class="payment-due-card">
        <div class="due-info">
            <p class="due-label"><i class="far fa-calendar-alt"></i> Next Payment Due: <strong><?php echo date('F j, Y', strtotime('last day of this month')); ?></strong></p>
            <p class="amount">Amount: <strong>₱ 1,000.00</strong></p>
        </div>
        <form method="POST" action="" class="pay-now-form">
            <div class="payment-method-select">
                <label>Payment Method</label>
                <select name="payment_method" class="method-select">
                    <option value="cash">Cash</option>
                    <option value="gcash">GCash</option>
                    <option value="card">Card</option>
                </select>
            </div>
            <button type="submit" name="pay_now" class="btn-pay-now">
                <i class="fas fa-credit-card"></i> PAY NOW
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Payment History -->
    <div class="payment-history" id="frame18">
        <h4>Payment History</h4>
        <?php if (empty($all_dues)): ?>
            <p style="color:#999; text-align:center; padding:2rem 0;">No payment records yet.</p>
        <?php else: ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_dues as $due): ?>
                <tr>
                    <td><?php echo date('F Y', strtotime($due['due_month'])); ?></td>
                    <td>₱ <?php echo number_format($due['amount'], 2); ?></td>
                    <td><?php echo $due['payment_method'] ? ucfirst($due['payment_method']) : '—'; ?></td>
                    <td><?php echo $due['paid_at'] ? date('M d, Y', strtotime($due['paid_at'])) : '—'; ?></td>
                    <td>
                        <span class="status-pill <?php echo $due['status'] === 'paid' ? 'paid' : 'not-paid'; ?>">
                            <?php echo strtoupper($due['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Bottom Action Buttons -->
    <div class="bottom-actions">
        <a href="monthly-dues.php" class="action-btn">
            <i class="fas fa-coins"></i>
            <span>Monthly Compensates</span>
        </a>
        <a href="#" class="action-btn" onclick="document.querySelector('.method-select')?.focus(); return false;">
            <i class="fas fa-credit-card"></i>
            <span>Payment Method</span>
        </a>
    </div>

    <div class="back-link">
        <a href="villager-dashboard.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>

<style>
.dues-container {
    background: white;
    border-radius: 30px;
    padding: 3rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.dues-container h2 { color: #2e7d32; font-size: 2.2rem; margin-bottom: 0.2rem; }
.dues-container h3 { color: #666; margin-bottom: 2rem; font-size: 1.5rem; display: flex; align-items: center; gap: 8px; }

/* Payment Due Card */
.payment-due-card {
    background: linear-gradient(135deg, #8bc34a, #6a994e);
    color: white;
    border-radius: 20px;
    padding: 2.5rem;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    box-shadow: 0 20px 40px rgba(139,195,74,0.3);
}

.payment-due-card.is-paid {
    background: linear-gradient(135deg, #43a047, #2e7d32);
}

.due-info p { font-size: 1.1rem; margin: 0.5rem 0; }
.due-label  { font-size: 1rem !important; opacity: 0.92; }
.amount     { font-size: 2rem !important; font-weight: 700; }

/* Pay now form */
.pay-now-form {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.8rem;
}

.payment-method-select {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.payment-method-select label {
    font-size: 0.9rem;
    opacity: 0.85;
    font-weight: 500;
}

.method-select {
    padding: 0.6rem 1rem;
    border-radius: 8px;
    border: none;
    font-family: 'Poppins', sans-serif;
    font-size: 1rem;
    color: #2e7d32;
    font-weight: 600;
    cursor: pointer;
    outline: none;
    min-width: 160px;
}

.btn-pay-now {
    background: white;
    color: #2e7d32;
    border: none;
    padding: 1rem 2.5rem;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Poppins', sans-serif;
}

.btn-pay-now:hover {
    background: #1b5e20;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.paid-badge {
    background: rgba(255,255,255,0.25);
    padding: 1.2rem 3rem;
    border-radius: 10px;
    font-size: 1.3rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Payment History */
.payment-history {
    background: #f9f9f9;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.payment-history h4 { font-size: 1.5rem; margin-bottom: 1.5rem; color: #2e7d32; }

.history-table { width: 100%; border-collapse: collapse; }

.history-table thead th {
    text-align: left;
    padding: 0.8rem 0.5rem;
    color: #888;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e0e0e0;
}

.history-table thead th:last-child { text-align: right; }

.history-table tr { border-bottom: 1px solid #e0e0e0; }
.history-table tbody tr:last-child { border-bottom: none; }
.history-table tbody tr:hover { background: #f0f8e8; }

.history-table td {
    padding: 1rem 0.5rem;
    font-size: 1rem;
    color: #444;
}

.history-table td:last-child { text-align: right; }

.status-pill {
    display: inline-block;
    padding: 0.3rem 0.9rem;
    border-radius: 12px;
    font-size: 0.82rem;
    font-weight: 700;
}

.status-pill.paid     { background: #e8f5e9; color: #2e7d32; }
.status-pill.not-paid { background: #ffebee; color: #f44336; }

/* Bottom action buttons */
.bottom-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.action-btn {
    flex: 1;
    min-width: 180px;
    background: #f5f5f5;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 1.2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: #444;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s;
    cursor: pointer;
    text-align: center;
}

.action-btn i {
    font-size: 1.6rem;
    color: #8bc34a;
}

.action-btn:hover {
    background: #e8f5e9;
    border-color: #8bc34a;
    color: #2e7d32;
    transform: translateY(-2px);
}

.action-btn:hover i { color: #2e7d32; }

/* Back button */
.back-link { text-align: center; margin-top: 1rem; }

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0.9rem 2rem;
    background: #f5f5f5;
    color: #2e7d32;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.btn-back:hover {
    background: #8bc34a;
    color: white;
    border-color: #8bc34a;
}

/* Responsive */
@media (max-width: 768px) {
    .payment-due-card { flex-direction: column; gap: 1.5rem; text-align: center; }
    .pay-now-form     { align-items: center; width: 100%; }
    .paid-badge       { justify-content: center; }
    .bottom-actions   { flex-direction: column; }
    .action-btn       { flex-direction: row; justify-content: center; }
    .history-table thead th:nth-child(3),
    .history-table td:nth-child(3) { display: none; }
}

@media (max-width: 480px) {
    .dues-container { padding: 1.5rem; }
    .btn-pay-now    { width: 100%; justify-content: center; }
    .history-table thead th:nth-child(4),
    .history-table td:nth-child(4) { display: none; }
}
</style>