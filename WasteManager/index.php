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
                <div class="contact-container">
                    <div class="contact-info">
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
                    <div class="contact-form">
                        <h3>Send us a message</h3>
                        <form id="contactForm" onsubmit="alert('Message sent! (Demo)'); return false;">
                            <input type="text" placeholder="Your Name" required>
                            <input type="email" placeholder="Your Email" required>
                            <textarea placeholder="Your Message" rows="5" required></textarea>
                            <button type="submit" class="btn-submit">Send Message</button>
                        </form>
                    </div>
                </div>
                <div class="back-link">
                    <a href="index.php" class="btn-back">← Back to Login</a>
                </div>
            </div>
            <?php
            break;
    }
    
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
                ':password' => $new_password,
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
                
                <!-- Error message divs - UPDATED with emailError -->
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
    document.getElementById('emailError').style.display = 'none'; // ADDED
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