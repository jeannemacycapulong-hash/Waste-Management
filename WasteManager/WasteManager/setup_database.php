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
    
    // Drop existing tables to start fresh (optional - remove if you want to keep data)
    echo "<p>Cleaning up old tables...</p>";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p style='color: green;'>✅ Old tables removed</p>";
    
    // =====================================================
    // Create tables
    // =====================================================
    echo "<p>Creating tables...</p>";
    
    // Users table
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        role ENUM('villager', 'collector', 'admin') NOT NULL DEFAULT 'villager',
        contact_number VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        is_active BOOLEAN DEFAULT TRUE
    )");
    echo "<p style='color: green;'>✅ Users table created</p>";
    
    // Collection schedules table
    $pdo->exec("CREATE TABLE collection_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        collection_day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
        collection_time TIME NOT NULL DEFAULT '08:30:00',
        location_area VARCHAR(255) NOT NULL,
        waste_types VARCHAR(100) DEFAULT 'dry,wet',
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color: green;'>✅ Collection schedules table created</p>";
    
    // Pickups table
    $pdo->exec("CREATE TABLE pickups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        villager_id INT NOT NULL,
        collector_id INT NULL,
        schedule_date DATE NOT NULL,
        scheduled_time TIME DEFAULT '08:30:00',
        actual_time TIME NULL,
        status ENUM('pending', 'completed', 'missed', 'no_waste') DEFAULT 'pending',
        waste_type_dry BOOLEAN DEFAULT FALSE,
        waste_type_wet BOOLEAN DEFAULT FALSE,
        notes TEXT,
        completed_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (villager_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (collector_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "<p style='color: green;'>✅ Pickups table created</p>";
    
    // Reports table
    $pdo->exec("CREATE TABLE reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reporter_id INT NOT NULL,
        reporter_type ENUM('villager', 'collector') NOT NULL,
        issue_type VARCHAR(50) NOT NULL,
        location VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        contact_number VARCHAR(20),
        photo_path VARCHAR(255),
        urgency ENUM('low', 'medium', 'high') DEFAULT 'low',
        status ENUM('pending', 'in_progress', 'resolved', 'closed') DEFAULT 'pending',
        admin_response TEXT,
        resolved_by INT NULL,
        resolved_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "<p style='color: green;'>✅ Reports table created</p>";
    
    // Notifications table
    $pdo->exec("CREATE TABLE notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        read_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "<p style='color: green;'>✅ Notifications table created</p>";
    
    // Broadcast notifications table
    $pdo->exec("CREATE TABLE broadcast_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        target ENUM('all', 'villagers', 'collectors', 'specific') NOT NULL,
        target_user_id INT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
        sent_by INT NOT NULL,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sent_by) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "<p style='color: green;'>✅ Broadcast notifications table created</p>";
    
    // Monthly dues table
    $pdo->exec("CREATE TABLE monthly_dues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        villager_id INT NOT NULL,
        due_month DATE NOT NULL,
        amount DECIMAL(10,2) NOT NULL DEFAULT 1000.00,
        status ENUM('paid', 'unpaid', 'overdue') DEFAULT 'unpaid',
        payment_date DATE NULL,
        payment_method VARCHAR(50) NULL,
        reference_number VARCHAR(100) NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (villager_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_villager_month (villager_id, due_month)
    )");
    echo "<p style='color: green;'>✅ Monthly dues table created</p>";
    
    // Collector routes table
    $pdo->exec("CREATE TABLE collector_routes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        collector_id INT NOT NULL,
        route_name VARCHAR(100) NOT NULL,
        area_description TEXT NOT NULL,
        estimated_households INT DEFAULT 0,
        estimated_time_minutes INT DEFAULT 240,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (collector_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "<p style='color: green;'>✅ Collector routes table created</p>";
    
    // Route assignments table
    $pdo->exec("CREATE TABLE route_assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        route_id INT NOT NULL,
        villager_id INT NOT NULL,
        sequence_order INT DEFAULT 0,
        block_number VARCHAR(50),
        lot_number VARCHAR(50),
        special_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (route_id) REFERENCES collector_routes(id) ON DELETE CASCADE,
        FOREIGN KEY (villager_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_villager_route (villager_id)
    )");
    echo "<p style='color: green;'>✅ Route assignments table created</p>";
    
    // System logs table
    $pdo->exec("CREATE TABLE system_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "<p style='color: green;'>✅ System logs table created</p>";
    
    // =====================================================
    // Insert sample data
    // =====================================================
    echo "<p>Inserting sample data...</p>";
    
    // Insert users
    $pdo->exec("INSERT INTO users (username, password, name, role, contact_number, address) VALUES
        ('user1', 'pass1', 'Juan Dela Cruz', 'villager', '09123456789', 'Blk 1 Lot 2, Pampang Purok, Angeles City'),
        ('villager', 'demo', 'Maria Santos', 'villager', '09187654321', 'Blk 2 Lot 5, Pampang Purok, Angeles City'),
        ('collector', 'demo', 'Pedro Reyes', 'collector', '09234567890', 'Blk 3 Lot 8, Pampang Purok, Angeles City'),
        ('admin', 'demo', 'Admin User', 'admin', '09345678901', 'Admin Office, Angeles City')");
    echo "<p style='color: green;'>✅ Users inserted</p>";
    
    // Insert collection schedules
    $pdo->exec("INSERT INTO collection_schedules (collection_day, collection_time, location_area, waste_types) VALUES
        ('Monday', '08:30:00', 'Baranggay Pampang Purok, Angeles City', 'dry,wet'),
        ('Thursday', '08:30:00', 'Baranggay Pampang Purok, Angeles City', 'dry,wet')");
    echo "<p style='color: green;'>✅ Collection schedules inserted</p>";
    
    // Insert collector routes
    $pdo->exec("INSERT INTO collector_routes (collector_id, route_name, area_description, estimated_households, estimated_time_minutes) VALUES
        (3, 'Pampang Purok Main Route', 'Baranggay Pampang Purok - Complete circuit including all blocks', 125, 240)");
    echo "<p style='color: green;'>✅ Collector routes inserted</p>";
    
    // Insert route assignments
    $pdo->exec("INSERT INTO route_assignments (route_id, villager_id, sequence_order, block_number, lot_number) VALUES
        (1, 1, 1, 'Block 1', 'Lot 2'),
        (1, 2, 2, 'Block 2', 'Lot 5')");
    echo "<p style='color: green;'>✅ Route assignments inserted</p>";
    
    // Insert pickups
    $pdo->exec("INSERT INTO pickups (villager_id, collector_id, schedule_date, status, waste_type_dry, waste_type_wet) VALUES
        (1, 3, CURDATE(), 'pending', TRUE, TRUE),
        (2, 3, CURDATE(), 'pending', TRUE, TRUE),
        (3, 3, CURDATE(), 'completed', TRUE, TRUE)");
    echo "<p style='color: green;'>✅ Pickups inserted</p>";
    
    // Insert monthly dues
    $pdo->exec("INSERT INTO monthly_dues (villager_id, due_month, amount, status) VALUES
        (1, DATE_FORMAT(CURDATE(), '%Y-%m-01'), 1000.00, 'unpaid'),
        (2, DATE_FORMAT(CURDATE(), '%Y-%m-01'), 1000.00, 'unpaid'),
        (1, DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'), 1000.00, 'paid'),
        (2, DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'), 1000.00, 'paid')");
    echo "<p style='color: green;'>✅ Monthly dues inserted</p>";
    
    // Insert notifications
    $pdo->exec("INSERT INTO notifications (user_id, title, message, type) VALUES
        (1, 'Collection Day Tomorrow', 'Tomorrow is your scheduled collection day. Please place your waste bins out by 8:30 AM.', 'info'),
        (2, 'Collection Day Tomorrow', 'Tomorrow is your scheduled collection day. Please place your waste bins out by 8:30 AM.', 'info'),
        (3, 'Route Assigned', 'You have been assigned to Pampang Purok Main Route for today.', 'success'),
        (4, 'New System Setup', 'Database has been successfully installed.', 'success')");
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
    
    echo "<p><a href='index.php'>Go to Login Page</a> | <a href='db_test.php'>Run Database Test</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}
?>