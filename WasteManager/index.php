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
    
    if (isset($valid_users[$username]) && $valid_users[$username] === $password) {
        $_SESSION['user'] = $username;
        header('Location: role-selection.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}

// Handle create account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_account'])) {
    $new_username = $_POST['new_username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_username) || empty($new_password)) {
        $error = 'Please fill in all fields';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (isset($valid_users[$new_username])) {
        $error = 'Username already exists';
    } else {
        // In a real app, you'd save to database
        // For demo, we'll just show success message
        $success = 'Account created successfully! You can now login.';
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
        
        <!-- Create Account Form (Hidden by default) -->
        <div id="createAccountForm" style="display: none; margin-top: 2rem; border-top: 1px solid #eee; padding-top: 2rem;">
            <h3 style="color: #333; margin-bottom: 1rem;">Create New Account</h3>
            <form method="POST" action="" onsubmit="return validatePassword()">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="new_username" placeholder="Choose Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="new_password" id="new_password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                </div>
                <small id="passwordMatch" style="color: #f44336; display: none;">Passwords do not match</small>
                <button type="submit" name="create_account" class="btn-login" style="background: #2196F3;">
                    Create Account <i class="fas fa-user-plus"></i>
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
}

function validatePassword() {
    var password = document.getElementById('new_password').value;
    var confirm = document.getElementById('confirm_password').value;
    
    if (password !== confirm) {
        document.getElementById('passwordMatch').style.display = 'block';
        return false;
    }
    return true;
}
</script>

<?php include 'footer.php'; ?>