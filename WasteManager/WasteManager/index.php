<?php
// index.php - Login Page
require_once 'config.php';

// Check if this is a page view (service, about, contact)
$page = $_GET['page'] ?? '';

// Handle page display for non-logged in users
if (in_array($page, ['service', 'about', 'contact'])) {
    include 'header.php';
    
    // Display the appropriate page
    switch($page) {
        case 'service':
            ?>
            <div class="info-page">
                <h1>Our Services</h1>
                <div class="services-grid">
                    <div class="service-card">
                        <i class="fas fa-trash-alt fa-3x"></i>
                        <h3>Garbage Collection</h3>
                        <p>Regular scheduled waste collection for all villages</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-recycle fa-3x"></i>
                        <h3>Recycling Program</h3>
                        <p>Separate collection for recyclable materials</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-calendar-check fa-3x"></i>
                        <h3>Schedule Management</h3>
                        <p>View and manage collection schedules</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-chart-line fa-3x"></i>
                        <h3>Reports & Analytics</h3>
                        <p>Track waste management performance</p>
                    </div>
                </div>
                <div class="back-link">
                    <a href="index.php" class="btn-back">← Back to Login</a>
                </div>
            </div>
            <?php
            break;
            
        case 'about':
            ?>
            <div class="info-page">
                <h1>About Us</h1>
                <div class="about-content">
                    <i class="fas fa-leaf fa-4x"></i>
                    <h2>Waste Management App</h2>
                    <p>We are dedicated to providing efficient waste management solutions for communities.</p>
                    <p>Our mission is to create cleaner and more sustainable villages through technology.</p>
                    <div class="stats-container">
                        <div class="stat">
                            <h3>50+</h3>
                            <p>Villages Served</p>
                        </div>
                        <div class="stat">
                            <h3>10k+</h3>
                            <p>Happy Residents</p>
                        </div>
                        <div class="stat">
                            <h3>2020</h3>
                            <p>Year Established</p>
                        </div>
                    </div>
                </div>
                <div class="back-link">
                    <a href="index.php" class="btn-back">← Back to Login</a>
                </div>
            </div>
            <?php
            break;
            
        case 'contact':
            ?>
            <div class="info-page">
                <h1>Contact Us</h1>
                <div class="contact-container-single">
                    <div class="contact-info-grid">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                            <h3>Address</h3>
                            <p>123 Waste Management St., Angeles City, Philippines</p>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone fa-2x"></i>
                            <h3>Phone</h3>
                            <p>(045) 123-4567</p>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope fa-2x"></i>
                            <h3>Email</h3>
                            <p>info@wastemanager.com</p>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-clock fa-2x"></i>
                            <h3>Office Hours</h3>
                            <p>Mon-Fri: 8:00 AM - 5:00 PM</p>
                        </div>
                    </div>
                </div>
                <div class="back-link">
                    <a href="index.php" class="btn-back">← Back to Login</a>
                </div>
            </div>
            <?php
            break;
    }
    
    ?>
    <style>
    /* ================================================
       INFO PAGES - Service / About / Contact
       ================================================ */
    .info-page {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: 0 30px 50px rgba(0,0,0,0.1);
    }

    .info-page h1 {
        color: #2e7d32;
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2.5rem;
    }

    /* Service Cards */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin: 2rem 0;
    }

    .service-card {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(139,195,74,0.25);
    }

    .service-card i {
        color: #8bc34a;
        margin-bottom: 1rem;
    }

    .service-card h3 {
        color: #2e7d32;
        margin-bottom: 0.5rem;
    }

    .service-card p {
        color: #666;
    }

    /* About Page */
    .about-content {
        text-align: center;
        padding: 2rem;
    }

    .about-content i {
        color: #8bc34a;
        margin-bottom: 1.5rem;
    }

    .about-content h2 {
        color: #2e7d32;
        margin: 1rem 0;
    }

    .about-content p {
        color: #666;
        font-size: 1.1rem;
        max-width: 800px;
        margin: 0.5rem auto;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 2rem;
        margin: 2rem 0;
    }

    .stat {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(139,195,74,0.25);
    }

    .stat h3 {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        color: #8bc34a;
    }

    .stat p {
        color: #555;
        font-weight: 500;
    }

    /* Contact Page */
    .contact-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 2rem;
        margin: 2rem 0;
    }

    .contact-item {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .contact-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(139,195,74,0.25);
    }

    .contact-item i {
        color: #8bc34a;
        margin-bottom: 1rem;
    }

    .contact-item h3 {
        color: #2e7d32;
        margin-bottom: 0.5rem;
        font-size: 1.3rem;
    }

    .contact-item p {
        color: #666;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .info-page { padding: 1.5rem; }
        .contact-info-grid { grid-template-columns: 1fr; }
        .stats-container { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
        .stats-container { grid-template-columns: 1fr; }
    }
    </style>
    <?php

    include 'footer.php';
    exit;
}

// Redirect if already logged in
if(isset($_SESSION['user'])) {
    header('Location: role-selection.php');
    exit;
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (loginUser($username, $password)) {
        header('Location: role-selection.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}

// Handle create account (enhanced version with full data collection)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_account'])) {
    $new_username = trim($_POST['new_username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $contact_number = trim($_POST['contact_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $agree_terms = isset($_POST['agree_terms']);
    
    // Validation
    $errors = [];
    
    if (empty($new_username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($new_username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }
    
    // Email validation
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($new_password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($new_password) < 4) {
        $errors[] = 'Password must be at least 4 characters';
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    if (!empty($contact_number) && !preg_match('/^[0-9]{11}$/', $contact_number)) {
        $errors[] = 'Contact number must be 11 digits';
    }
    
    if (!$agree_terms) {
        $errors[] = 'You must agree to the Terms and Conditions';
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Check username
            $check_username = "SELECT id FROM users WHERE username = :username";
            $stmt = $db->prepare($check_username);
            $stmt->execute([':username' => $new_username]);
            
            if ($stmt->fetch()) {
                $errors[] = 'Username already exists';
            }
            
            // Check email
            $check_email = "SELECT id FROM users WHERE email = :email";
            $stmt = $db->prepare($check_email);
            $stmt->execute([':email' => $email]);
            
            if ($stmt->fetch()) {
                $errors[] = 'Email already registered';
            }
            
        } catch (Exception $e) {
            error_log("Database check error: " . $e->getMessage());
            // Fallback to array check
            if (isset($valid_users[$new_username])) {
                $errors[] = 'Username already exists';
            }
        }
    }
    
    // If no errors, create the account
    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Include email in INSERT query
            $query = "INSERT INTO users (username, email, password, name, role, contact_number, address, created_at, is_active) 
                      VALUES (:username, :email, :password, :name, 'villager', :contact, :address, NOW(), 1)";
            
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                ':username' => $new_username,
                ':email' => $email,
                ':password' => password_hash($new_password, PASSWORD_DEFAULT),
                ':name' => $full_name,
                ':contact' => $contact_number ?: null,
                ':address' => $address ?: null
            ]);
            
            if ($result) {
                $new_user_id = $db->lastInsertId();
                
                // Create initial monthly due for current month
                try {
                    $due_query = "INSERT INTO monthly_dues (villager_id, due_month, amount, status) 
                                  VALUES (:villager_id, DATE_FORMAT(CURDATE(), '%Y-%m-01'), 1000.00, 'unpaid')";
                    $due_stmt = $db->prepare($due_query);
                    $due_stmt->execute([':villager_id' => $new_user_id]);
                } catch (Exception $e) {
                    error_log("Failed to create initial due: " . $e->getMessage());
                }
                
                // Send welcome notification
                addNotification(
                    $new_user_id,
                    'Welcome to WasteManager!',
                    'Your account has been created successfully. You can now log in and access your villager dashboard.',
                    'success'
                );
                
                // Notify admin
                $admin_query = "SELECT id FROM users WHERE role = 'admin'";
                $admin_stmt = $db->query($admin_query);
                while ($admin = $admin_stmt->fetch()) {
                    addNotification(
                        $admin['id'],
                        'New Villager Registered',
                        "New villager '$full_name' has registered with email: $email",
                        'info'
                    );
                }
                
                $success = 'Account created successfully! You can now login.';
                
                // Auto-fill login form with new username
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.querySelector('input[name=\"username\"]').value = '" . addslashes($new_username) . "';
                        showLoginForm();
                    });
                </script>";
            } else {
                $error = 'Failed to create account. Please try again.';
            }
            
        } catch (Exception $e) {
            error_log("Database insert error: " . $e->getMessage());
            $error = 'Database error occurred. Please try again.';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

include 'header.php';
?>

<div class="login-container">
    <div class="login-card">
        <h1>Welcome To Our <span class="highlight">Application</span></h1>
        <p class="subtitle">Sign in to manage waste collection</p>
        
        <?php if($error): ?>
            <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="" class="login-form" id="loginForm">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" name="login" class="btn-login">
                Login <i class="fas fa-arrow-right"></i>
            </button>
        </form>
        
        <p class="create-account-link">
            <a href="#" onclick="showCreateAccount(); return false;">Create new account</a>
        </p>
        
        <!-- Create Account Form (Updated with full fields) -->
        <div id="createAccountForm" style="display: none; margin-top: 2rem; border-top: 1px solid #eee; padding-top: 2rem;">
            <h3 style="color: #333; margin-bottom: 1rem;">Create New Villager Account</h3>
            <form method="POST" action="" onsubmit="return validateRegistration()">
                <!-- Username -->
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="new_username" id="new_username" placeholder="Username *" required>
                </div>
                
                <!-- Email Field -->
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Email Address *" required>
                </div>
                
                <!-- Full Name -->
                <div class="input-group">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="full_name" id="full_name" placeholder="Full Name *" required>
                </div>
                
                <!-- Password -->
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="new_password" id="new_password" placeholder="Password *" required>
                </div>
                
                <!-- Confirm Password -->
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password *" required>
                </div>
                
                <!-- Contact Number -->
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="contact_number" id="contact_number" placeholder="Contact Number (e.g., 09123456789)" pattern="[0-9]{11}" title="Please enter a valid 11-digit mobile number">
                </div>
                
                <!-- Address -->
                <div class="input-group" style="height: auto; padding: 0.5rem 1rem;">
                    <i class="fas fa-map-marker-alt" style="margin-top: 0.8rem;"></i>
                    <textarea name="address" id="address" rows="3" placeholder="Complete Address (e.g., Blk 1 Lot 2, Pampang Purok, Angeles City)" style="width: 100%; padding: 0.8rem 0; border: none; background: transparent; resize: vertical;"></textarea>
                </div>
                
                <!-- Terms and Conditions -->
                <div style="margin: 1rem 0;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="agree_terms" id="agree_terms" required>
                        <span style="font-size: 0.9rem; color: #555;">I agree to the <a href="#" onclick="showTerms(); return false;">Terms and Conditions</a> *</span>
                    </label>
                </div>
                
                <!-- Error message divs -->
                <div id="passwordMatch" style="color: #f44336; font-size: 0.9rem; margin-bottom: 1rem; display: none;">Passwords do not match</div>
                <div id="usernameError" style="color: #f44336; font-size: 0.9rem; margin-bottom: 1rem; display: none;">Username already exists</div>
                <div id="emailError" style="color: #f44336; font-size: 0.9rem; margin-bottom: 1rem; display: none;">Email already registered</div>
                
                <button type="submit" name="create_account" class="btn-login" style="background: #2196F3;">
                    <i class="fas fa-user-plus"></i> Create Villager Account
                </button>
            </form>
            <p style="text-align: center; margin-top: 1rem;">
                <a href="#" onclick="hideCreateAccount(); return false;">← Back to Login</a>
            </p>
        </div>
        
        <p class="demo-credentials">
            Demo: user1/pass1, villager/demo, collector/demo, admin/demo
        </p>
    </div>
</div>

<script>
function showCreateAccount() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('createAccountForm').style.display = 'block';
    document.querySelector('.create-account-link').style.display = 'none';
}

function hideCreateAccount() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('createAccountForm').style.display = 'none';
    document.querySelector('.create-account-link').style.display = 'block';
    
    // Clear form
    document.getElementById('createAccountForm').querySelector('form').reset();
    document.getElementById('passwordMatch').style.display = 'none';
    document.getElementById('usernameError').style.display = 'none';
    document.getElementById('emailError').style.display = 'none';
}

function showLoginForm() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('createAccountForm').style.display = 'none';
    document.querySelector('.create-account-link').style.display = 'block';
}

function validateRegistration() {
    const password = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;
    const username = document.getElementById('new_username').value;
    const email = document.getElementById('email').value;
    const fullName = document.getElementById('full_name').value;
    const contact = document.getElementById('contact_number').value;
    const agree = document.getElementById('agree_terms').checked;
    
    // Reset error messages
    document.getElementById('passwordMatch').style.display = 'none';
    
    // Validate password match
    if (password !== confirm) {
        document.getElementById('passwordMatch').style.display = 'block';
        return false;
    }
    
    // Validate username length
    if (username.length < 3) {
        alert('Username must be at least 3 characters long');
        return false;
    }
    
    // Validate username format
    const usernameRegex = /^[a-zA-Z0-9_]+$/;
    if (!usernameRegex.test(username)) {
        alert('Username can only contain letters, numbers, and underscores');
        return false;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    // Validate full name
    if (fullName.trim().length < 2) {
        alert('Please enter your full name');
        return false;
    }
    
    // Validate password length
    if (password.length < 4) {
        alert('Password must be at least 4 characters long');
        return false;
    }
    
    // Validate contact number if provided
    if (contact && !/^[0-9]{11}$/.test(contact)) {
        alert('Please enter a valid 11-digit contact number');
        return false;
    }
    
    // Validate terms agreement
    if (!agree) {
        alert('You must agree to the Terms and Conditions');
        return false;
    }
    
    return true;
}

function showTerms() {
    alert('Terms and Conditions:\n\n1. You must be a resident of the service area.\n2. You agree to follow waste segregation guidelines.\n3. Monthly dues must be paid on time.\n4. Report any issues promptly.\n5. Treat collection staff with respect.');
}

// Real-time username availability check
let usernameCheckTimer;
function checkUsernameAvailability() {
    clearTimeout(usernameCheckTimer);
    const username = document.getElementById('new_username').value;
    
    if (username.length < 3) return;
    
    usernameCheckTimer = setTimeout(function() {
        fetch('check_username.php?username=' + encodeURIComponent(username))
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.getElementById('usernameError').style.display = 'block';
                } else {
                    document.getElementById('usernameError').style.display = 'none';
                }
            })
            .catch(() => {});
    }, 500);
}

// Real-time email availability check
let emailCheckTimer;
function checkEmailAvailability() {
    clearTimeout(emailCheckTimer);
    const email = document.getElementById('email').value;
    
    if (email.length < 5 || !email.includes('@')) return;
    
    emailCheckTimer = setTimeout(function() {
        fetch('check_email.php?email=' + encodeURIComponent(email))
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.getElementById('emailError').style.display = 'block';
                } else {
                    document.getElementById('emailError').style.display = 'none';
                }
            })
            .catch(() => {});
    }, 500);
}

// Add event listeners
document.getElementById('new_username')?.addEventListener('input', checkUsernameAvailability);
document.getElementById('email')?.addEventListener('input', checkEmailAvailability);
</script>

<?php include 'footer.php'; ?>
<style>
/* Login Page Styles */
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
}

.login-card {
    background: white;
    padding: 3rem 2.5rem;
    border-radius: 20px;
    box-shadow: 0 30px 50px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 450px;
    text-align: center;
}

.login-card h1 {
    color: #2e7d32;
    font-size: 2.2rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    line-height: 1.3;
}

.login-card h1 .highlight {
    color: #8bc34a;
    display: block;
    font-size: 2.5rem;
    margin-top: 0.2rem;
}

.subtitle {
    color: #666;
    margin-bottom: 2rem;
    font-size: 1rem;
}

.login-form {
    margin-top: 1.5rem;
}

.input-group {
    display: flex;
    align-items: center;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    margin-bottom: 1.2rem;
    padding: 0 1rem;
    background: #f9f9f9;
    transition: border-color 0.3s;
}

.input-group:focus-within {
    border-color: #8bc34a;
}

.input-group i {
    color: #8bc34a;
    font-size: 1.1rem;
    margin-right: 0.5rem;
}

.input-group input {
    width: 100%;
    padding: 1rem 0;
    border: none;
    background: transparent;
    outline: none;
    font-size: 1rem;
}

.create-account-link {
    text-align: center;
    margin: 1rem 0;
}

.create-account-link a {
    color: #8bc34a;
    text-decoration: none;
    font-weight: 500;
}

.create-account-link a:hover {
    text-decoration: underline;
    color: #2e7d32;
}

.btn-login {
    width: 100%;
    padding: 1rem;
    background: #8bc34a;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.2rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    letter-spacing: 1px;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-login:hover {
    background: #2e7d32;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(46, 125, 50, 0.3);
}

.demo-credentials {
    font-size: 0.85rem;
    color: #888;
    background: #f5f5f5;
    padding: 0.8rem;
    border-radius: 8px;
    text-align: center;
    margin-top: 2rem;
    line-height: 1.5;
}

/* Password match message */
#passwordMatch {
    display: block;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #f44336;
}

/* Service Page */
.info-page {
    background: white;
    border-radius: 30px;
    padding: 3rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.info-page h1 {
    color: #2e7d32;
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.5rem;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
}

.service-card {
    background: #f9f9f9;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    transition: transform 0.3s;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(139, 195, 74, 0.2);
}

.service-card i {
    color: #8bc34a;
    margin-bottom: 1rem;
}

.service-card h3 {
    color: #2e7d32;
    margin-bottom: 0.5rem;
}

.service-card p {
    color: #666;
}

/* About Page */
.about-content {
    text-align: center;
    padding: 2rem;
}

.about-content i {
    color: #8bc34a;
    margin-bottom: 1.5rem;
}

.about-content h2 {
    color: #2e7d32;
    margin: 1rem 0;
}

.about-content p {
    color: #666;
    font-size: 1.1rem;
    max-width: 800px;
    margin: 0.5rem auto;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
}

.stat {
    background: linear-gradient(135deg, #8bc34a, #6a994e);
    color: white;
    padding: 2rem;
    border-radius: 15px;
}

.stat h3 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

/* Contact Page */
.contact-container {
    max-width: 1000px;
    margin: 3rem auto;
}

.contact-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
}

.contact-item {
    background: #f9f9f9;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.contact-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(139, 195, 74, 0.2);
}

.contact-item i {
    color: #8bc34a;
    margin-bottom: 1rem;
}

.contact-item h3 {
    color: #2e7d32;
    margin-bottom: 0.5rem;
    font-size: 1.3rem;
}

.contact-item p {
    color: #666;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .login-card {
        padding: 2rem 1.5rem;
    }

    .contact-info-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .contact-item {
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .login-card h1 {
        font-size: 1.8rem;
    }

    .login-card h1 .highlight {
        font-size: 2rem;
    }

    .info-page {
        padding: 1.5rem;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .contact-container {
        margin: 2rem auto;
    }

    .contact-item {
        padding: 1.2rem;
    }

    .contact-item i {
        font-size: 1.8rem;
    }

    .contact-item h3 {
        font-size: 1.2rem;
    }

    .contact-item p {
        font-size: 0.95rem;
    }
}
</style>