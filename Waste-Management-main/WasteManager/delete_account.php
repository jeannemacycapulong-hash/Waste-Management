<?php
// delete_account.php - Villager self-service account deletion
require_once 'config.php';
requireLogin();

// Only villagers may access this page
if (getUserRole() !== 'villager') {
    header('Location: role-selection.php');
    exit;
}

$user_id   = getUserId();
$user_name = getUserName();
$error     = '';

// Handle deletion form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $password = $_POST['password'] ?? '';

    if (empty($password)) {
        $error = 'Please enter your password to confirm.';
    } else {
        $result = deleteVillagerAccount($user_id, $password);

        if ($result['success']) {
            // Destroy the session and redirect to a farewell page
            session_destroy();
            header('Location: index.php?deleted=1');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

include 'header.php';
?>

<div class="delete-account-container">
    <div class="delete-header">
        <div class="delete-icon-wrap">
            <i class="fas fa-user-times"></i>
        </div>
        <h2>Delete My Account</h2>
        <p class="subtitle">We're sorry to see you go.</p>
    </div>

    <?php if ($error): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Consequences warning -->
    <div class="warning-card">
        <h4><i class="fas fa-exclamation-triangle"></i> Before you proceed, please note:</h4>
        <ul>
            <li><i class="fas fa-times-circle"></i> Your account <strong>(<?php echo htmlspecialchars($user_name); ?>)</strong> will be <strong>permanently deleted</strong>.</li>
            <li><i class="fas fa-times-circle"></i> All your <strong>payment history</strong> and monthly dues records will be erased.</li>
            <li><i class="fas fa-times-circle"></i> Any <strong>issue reports</strong> you have submitted will be removed.</li>
            <li><i class="fas fa-times-circle"></i> All your <strong>notifications</strong> will be deleted.</li>
            <li><i class="fas fa-undo-alt"></i> <strong>This action cannot be undone.</strong></li>
        </ul>
    </div>

    <!-- Confirmation form -->
    <div class="confirm-form-card">
        <h4><i class="fas fa-lock"></i> Confirm with your password</h4>
        <p>Enter your current password to permanently delete your account.</p>

        <form method="POST" action="" id="deleteForm">
            <div class="input-group">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="toggle-pw" onclick="togglePassword()">
                        <i class="fas fa-eye" id="pw-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <a href="villager-dashboard.php" class="btn-cancel">
                    <i class="fas fa-arrow-left"></i> Cancel, Keep My Account
                </a>
                <button type="button" class="btn-delete" onclick="confirmDeletion()">
                    <i class="fas fa-trash-alt"></i> Yes, Delete My Account
                </button>
            </div>

            <!-- Hidden submit button triggered after modal confirmation -->
            <button type="submit" name="confirm_delete" id="realSubmit" style="display:none;"></button>
        </form>
    </div>
</div>

<!-- Final confirmation modal -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h3>Are you absolutely sure?</h3>
        <p>This will permanently delete <strong><?php echo htmlspecialchars($user_name); ?></strong>'s account. There is <strong>no way to recover</strong> your data after this.</p>
        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeModal()">No, Go Back</button>
            <button class="btn-modal-confirm" onclick="submitDeletion()">Yes, Delete Forever</button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<style>
.delete-account-container {
    background: white;
    border-radius: 30px;
    padding: 3rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
    max-width: 680px;
    margin: 0 auto;
}

/* Header */
.delete-header {
    text-align: center;
    margin-bottom: 2rem;
}

.delete-icon-wrap {
    width: 90px;
    height: 90px;
    background: #ffebee;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.2rem;
}

.delete-icon-wrap i {
    font-size: 2.8rem;
    color: #e53935;
}

.delete-header h2 {
    color: #c62828;
    font-size: 2rem;
    margin-bottom: 0.3rem;
}

.delete-header .subtitle {
    color: #888;
    font-size: 1.1rem;
}

/* Warning card */
.warning-card {
    background: #fff8e1;
    border: 2px solid #ffcc02;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
}

.warning-card h4 {
    color: #e65100;
    margin-bottom: 1rem;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.warning-card ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.warning-card li {
    padding: 0.55rem 0;
    color: #5d4037;
    font-size: 0.97rem;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    border-bottom: 1px dashed #ffe082;
}

.warning-card li:last-child {
    border-bottom: none;
}

.warning-card li i {
    color: #e53935;
    margin-top: 3px;
    min-width: 16px;
}

.warning-card li i.fa-undo-alt {
    color: #f57c00;
}

/* Confirm form card */
.confirm-form-card {
    background: #fafafa;
    border-radius: 16px;
    padding: 2rem;
    border: 2px solid #f5f5f5;
}

.confirm-form-card h4 {
    color: #333;
    font-size: 1.15rem;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.confirm-form-card > p {
    color: #777;
    margin-bottom: 1.5rem;
    font-size: 0.97rem;
}

.input-group {
    margin-bottom: 1.8rem;
}

.input-group label {
    display: block;
    font-weight: 600;
    color: #444;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.password-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.password-wrap input {
    width: 100%;
    padding: 0.9rem 3rem 0.9rem 1.2rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 1rem;
    font-family: 'Poppins', sans-serif;
    transition: border-color 0.3s;
    outline: none;
}

.password-wrap input:focus {
    border-color: #e53935;
}

.toggle-pw {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    cursor: pointer;
    color: #aaa;
    font-size: 1.1rem;
    padding: 0;
    transition: color 0.2s;
}

.toggle-pw:hover {
    color: #555;
}

/* Action buttons */
.form-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-cancel {
    flex: 1;
    padding: 1rem 1.5rem;
    background: #f5f5f5;
    color: #2e7d32;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.97rem;
    text-align: center;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 2px solid transparent;
}

.btn-cancel:hover {
    background: #e8f5e9;
    border-color: #8bc34a;
    color: #2e7d32;
}

.btn-delete {
    flex: 1;
    padding: 1rem 1.5rem;
    background: #e53935;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 0.97rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-delete:hover {
    background: #c62828;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(229, 57, 53, 0.4);
}

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(3px);
}

.modal-overlay.active {
    display: flex;
}

.modal-box {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    max-width: 440px;
    width: 90%;
    text-align: center;
    box-shadow: 0 30px 60px rgba(0,0,0,0.25);
    animation: modalPop 0.25s ease-out;
}

@keyframes modalPop {
    from { transform: scale(0.85); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}

.modal-icon {
    font-size: 3rem;
    color: #e53935;
    margin-bottom: 1rem;
}

.modal-box h3 {
    color: #c62828;
    font-size: 1.5rem;
    margin-bottom: 0.8rem;
}

.modal-box p {
    color: #666;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.modal-actions {
    display: flex;
    gap: 1rem;
}

.btn-modal-cancel {
    flex: 1;
    padding: 0.9rem;
    background: #f5f5f5;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 0.97rem;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    color: #555;
    transition: all 0.3s;
}

.btn-modal-cancel:hover {
    background: #e8f5e9;
    border-color: #8bc34a;
    color: #2e7d32;
}

.btn-modal-confirm {
    flex: 1;
    padding: 0.9rem;
    background: #e53935;
    border: none;
    border-radius: 10px;
    font-size: 0.97rem;
    font-weight: 700;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    color: white;
    transition: all 0.3s;
}

.btn-modal-confirm:hover {
    background: #c62828;
    box-shadow: 0 5px 15px rgba(229,57,53,0.4);
}

/* Responsive */
@media (max-width: 600px) {
    .delete-account-container { padding: 1.5rem; }
    .form-actions { flex-direction: column; }
    .modal-actions { flex-direction: column-reverse; }
}
</style>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const eye   = document.getElementById('pw-eye');
    if (input.type === 'password') {
        input.type = 'text';
        eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function confirmDeletion() {
    const password = document.getElementById('password').value.trim();
    if (!password) {
        document.getElementById('password').focus();
        return;
    }
    document.getElementById('confirmModal').classList.add('active');
}

function closeModal() {
    document.getElementById('confirmModal').classList.remove('active');
}

function submitDeletion() {
    document.getElementById('realSubmit').click();
}

// Close modal on overlay click
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>