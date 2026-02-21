<?php
// setup_database.php - Run this to create tables
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'wastemanager';
$username = 'root';
$password = '';

echo "<h1>WasteManager Database Setup</h1>";

try {
    // Connect to MySQL (without database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    echo "<p>Creating database if needed...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database '$dbname' ready</p>";
    
    // Select the database
    $pdo->exec("USE `$dbname`");
    
    // Drop existing tables to start fresh (optional)
    echo "<p>Cleaning up old tables...</p>";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p style='color: green;'>✅ Old tables removed</p>";
    
    // =====================================================
    // Create JUST 3 tables
    // =====================================================
    echo "<p>Creating tables...</p>";
    
    // 1. Users table
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        role ENUM('villager', 'collector', 'admin') NOT NULL DEFAULT 'villager',
        email VARCHAR(100) UNIQUE,
        contact_number VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        is_active BOOLEAN DEFAULT TRUE
    )");
    echo "<p style='color: green;'>✅ Users table created</p>";
    
    // 2. Reports table
    $pdo->exec("CREATE TABLE reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reporter_id INT NOT NULL,
        reporter_type ENUM('villager', 'collector') NOT NULL,
        issue_type VARCHAR(50) NOT NULL,
        location VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        contact_number VARCHAR(20),
        urgency ENUM('low', 'medium', 'high') DEFAULT 'low',
        status ENUM('pending', 'resolved') DEFAULT 'pending',
        admin_response TEXT,
        resolved_by INT NULL,
        resolved_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "<p style='color: green;'>✅ Reports table created</p>";
    
    // 3. Notifications table
    $pdo->exec("CREATE TABLE notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info', 'success', 'warning') DEFAULT 'info',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        read_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "<p style='color: green;'>✅ Notifications table created</p>";
    
    // =====================================================
    // Insert sample data
    // =====================================================
    echo "<p>Inserting sample data...</p>";
    
    // Insert users
    $pdo->exec("INSERT INTO users (username, password, name, role, email, contact_number, address) VALUES
        ('user1', 'pass1', 'Juan Dela Cruz', 'villager', 'juan@email.com', '09123456789', 'Blk 1 Lot 2, Pampang Purok, Angeles City'),
        ('villager', 'demo', 'Maria Santos', 'villager', 'maria@email.com', '09187654321', 'Blk 2 Lot 5, Pampang Purok, Angeles City'),
        ('collector', 'demo', 'Pedro Reyes', 'collector', 'pedro@email.com', '09234567890', 'Blk 3 Lot 8, Pampang Purok, Angeles City'),
        ('admin', 'demo', 'Admin User', 'admin', 'admin@email.com', '09345678901', 'Admin Office, Angeles City')");
    echo "<p style='color: green;'>✅ Users inserted</p>";
    
    // Insert sample reports
    $pdo->exec("INSERT INTO reports (reporter_id, reporter_type, issue_type, location, description, urgency) VALUES
        (1, 'villager', 'missed_collection', 'Blk 1 Lot 2, Pampang Purok', 'Collection was missed on Thursday', 'medium'),
        (2, 'villager', 'spilled_garbage', 'Blk 2 Lot 5, Pampang Purok', 'Garbage spilled on the road', 'high'),
        (3, 'collector', 'vehicle_issue', 'Baranggay Pampang Purok', 'Truck needs maintenance', 'low')");
    echo "<p style='color: green;'>✅ Reports inserted</p>";
    
    // Insert sample notifications
    $pdo->exec("INSERT INTO notifications (user_id, title, message, type) VALUES
        (1, 'Welcome!', 'Your account has been created successfully.', 'success'),
        (2, 'Collection Day Tomorrow', 'Please place your waste bins out by 8:30 AM.', 'info'),
        (3, 'Route Assigned', 'You have been assigned to Pampang Purok route.', 'info'),
        (4, 'System Ready', 'Waste Management System is now active.', 'success')");
    echo "<p style='color: green;'>✅ Notifications inserted</p>";
    
    // =====================================================
    // Summary
    // =====================================================
    echo "<h2 style='color: green;'>✅ Setup Complete!</h2>";
    
    // Count tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Created " . count($tables) . " tables successfully:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "<li>$table - $count records</li>";
    }
    echo "</ul>";
    
    echo "<p><strong>Demo Credentials:</strong></p>";
    echo "<ul>";
    echo "<li>user1 / pass1 (Villager)</li>";
    echo "<li>villager / demo (Villager)</li>";
    echo "<li>collector / demo (Collector)</li>";
    echo "<li>admin / demo (Admin)</li>";
    echo "</ul>";
    
    echo "<p><a href='index.php'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>