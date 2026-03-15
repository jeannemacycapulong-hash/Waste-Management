<?php
// header.php - Reusable header
// Add this at the top to get notification count
if (isset($_SESSION['user'])) {
    // Use the database function
    $notif_count = count(getUserNotifications(getUserId()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Management App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
/* ================================================
   GLOBAL STYLES - Light Green Theme
   ================================================ */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(135deg, #6a994e 0%, #558b2f 100%);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Header & Navigation */
.site-header {
    background: #8bc34a;
    color: white;
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    max-width: 1300px;
    margin: 0 auto;
}

.logo {
    font-size: 1.8rem;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.logo a {
    color: white;
    text-decoration: none;
}

.nav-menu {
    display: flex;
    list-style: none;
    gap: 2.5rem;
    align-items: center;
}

.nav-menu a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    font-size: 1.1rem;
    opacity: 0.9;
}

.nav-menu a:hover,
.nav-menu a.active {
    opacity: 1;
    color: #1b5e20;
    transform: translateY(-2px);
    font-weight: 600;
}

.logout-link {
    background: rgba(255,255,255,0.2);
    padding: 0.5rem 1.5rem;
    border-radius: 30px;
}

.logout-link:hover {
    background: rgba(255,255,255,0.3);
}

.hamburger {
    display: none;
    font-size: 1.8rem;
    cursor: pointer;
    color: white;
}

/* Main content area */
main {
    flex: 1;
    padding: 2rem;
    max-width: 1300px;
    margin: 0 auto;
    width: 100%;
}

/* Footer */
.site-footer {
    background: #a5d6a5;
    color: #1b5e20;
    text-align: center;
    padding: 1.5rem;
    margin-top: auto;
    font-size: 0.95rem;
    font-weight: 500;
}

/* Error and Success Messages */
.error-message, .success-message {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.error-message {
    background: #ffebee;
    color: #c62828;
}

.success-message {
    background: #e8f5e9;
    color: #2e7d32;
}

/* Back Button */
.btn-back {
    display: inline-block;
    padding: 1rem 2rem;
    background: #f5f5f5;
    color: #2e7d32;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-back:hover {
    background: #8bc34a;
    color: white;
}

.back-link {
    text-align: center;
    margin-top: 2rem;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-card,
.role-card,
.dashboard,
.dues-container,
.info-page {
    animation: fadeIn 0.6s ease-out;
}

/* Responsive - Navigation */
@media (max-width: 768px) {
    .nav-menu {
        display: none;
        width: 100%;
        flex-direction: column;
        padding: 1.5rem 0;
        gap: 1rem;
        background: #8bc34a;
        border-radius: 10px;
        margin-top: 1rem;
    }

    .nav-menu.active {
        display: flex;
    }

    .hamburger {
        display: block;
    }
}

@media (max-width: 480px) {
    main {
        padding: 1rem;
    }
}

/* Notification Bell */
.notification-item {
    position: relative;
}

.notification-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    font-size: 1.3rem;
    color: white !important;
    opacity: 0.9;
    transition: all 0.3s;
}

.notification-link:hover {
    opacity: 1;
    transform: translateY(-2px);
}

.notification-link.has-unread .fa-bell {
    color: #ff4444 !important;
    filter: drop-shadow(0 0 4px rgba(255, 68, 68, 0.7));
    animation: bell-shake 1.5s ease infinite;
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -10px;
    background: #ff4444;
    color: white;
    font-size: 0.65rem;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
    border: 2px solid #8bc34a;
    line-height: 1;
}

@keyframes bell-shake {
    0%, 100% { transform: rotate(0deg); }
    10%, 30% { transform: rotate(-12deg); }
    20%, 40% { transform: rotate(12deg); }
    50% { transform: rotate(0deg); }
}
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">WasteManager</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($_GET['page']) ? 'active' : ''; ?>">Home</a></li>
                <li><a href="index.php?page=service" class="<?php echo isset($_GET['page']) && $_GET['page'] == 'service' ? 'active' : ''; ?>">Service</a></li>
                <li><a href="index.php?page=about" class="<?php echo isset($_GET['page']) && $_GET['page'] == 'about' ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="index.php?page=contact" class="<?php echo isset($_GET['page']) && $_GET['page'] == 'contact' ? 'active' : ''; ?>">Contact</a></li>

                <?php if(isset($_SESSION['user'])): ?>
                    <!-- Notification Bell -->
                    <li class="notification-item">
                        <a href="notifications.php" class="notification-link <?php echo $notif_count > 0 ? 'has-unread' : ''; ?>">
                            <i class="fas fa-bell"></i>
                            <?php if($notif_count > 0): ?>
                                <span class="notification-badge"><?php echo $notif_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="logout.php" class="logout-link">Logout</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger"><i class="fas fa-bars"></i></div>
        </nav>
    </header>
    <main>