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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                        <a href="notifications.php" class="notification-link">
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