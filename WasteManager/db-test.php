<?php
// db_test.php - Test if database is working
require_once 'config.php';

// Only allow access from localhost for security
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Access denied');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Test</title>
    <style>
        body { font-family: Arial; max-width: 1000px; margin: 20px auto; padding: 20px; }
        .success { color: green; background: #e8f5e9; padding: 10px; border-radius: 5px; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #8bc34a; color: white; }
        .section { background: #f5f5f5; padding: 15px; margin: 20px 0; border-radius: 10px; }
    </style>
</head>
<body>
    <h1>WasteManager Database Test</h1>";

try {
    $db = getDB();
    echo "<div class='success'>✅ Database connected successfully</div>";
    
    // Test 1: Check if tables exist
    echo "<div class='section'>";
    echo "<h2>Test 1: Tables</h2>";
    
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (count($tables) > 0) {
        echo "<p class='success'>✅ Found " . count($tables) . " tables:</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ No tables found!</p>";
    }
    echo "</div>";
    
    // Test 2: Check users table
    echo "<div class='section'>";
    echo "<h2>Test 2: Users Table</h2>";
    
    $users = $db->query("SELECT id, username, name, role, created_at FROM users")->fetchAll();
    if (count($users) > 0) {
        echo "<p class='success'>✅ Found " . count($users) . " users:</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Role</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['name'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ No users found!</p>";
    }
    echo "</div>";
    
    // Test 3: Check pickups table
    echo "<div class='section'>";
    echo "<h2>Test 3: Pickups Table</h2>";
    
    $pickups = $db->query("SELECT p.*, v.name as villager_name 
                           FROM pickups p
                           JOIN users v ON p.villager_id = v.id
                           LIMIT 5")->fetchAll();
    
    if (count($pickups) > 0) {
        echo "<p class='success'>✅ Found pickups:</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Villager</th><th>Date</th><th>Status</th></tr>";
        foreach ($pickups as $pickup) {
            echo "<tr>";
            echo "<td>" . $pickup['id'] . "</td>";
            echo "<td>" . $pickup['villager_name'] . "</td>";
            echo "<td>" . $pickup['schedule_date'] . "</td>";
            echo "<td>" . $pickup['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No pickups found (this might be normal if none scheduled)</p>";
    }
    echo "</div>";
    
    // Test 4: Test login function with database
    echo "<div class='section'>";
    echo "<h2>Test 4: Login Function Test</h2>";
    
    // Clear any existing session
    session_destroy();
    session_start();
    
    // Test admin login
    if (loginUser('admin', 'demo')) {
        echo "<p class='success'>✅ Admin login successful</p>";
        echo "<p>User ID: " . getUserId() . "</p>";
        echo "<p>User Role: " . getUserRole() . "</p>";
        echo "<p>User Name: " . getUserName() . "</p>";
    } else {
        echo "<p class='error'>❌ Admin login failed</p>";
    }
    
    // Test collector login
    if (loginUser('collector', 'demo')) {
        echo "<p class='success'>✅ Collector login successful</p>";
    } else {
        echo "<p class='error'>❌ Collector login failed</p>";
    }
    echo "</div>";
    
    // Test 5: Check notifications
    echo "<div class='section'>";
    echo "<h2>Test 5: Notifications</h2>";
    
    // Get admin ID
    $admin = $db->query("SELECT id FROM users WHERE username = 'admin'")->fetch();
    if ($admin) {
        $notifications = getUserNotifications($admin['id']);
        echo "<p>Admin has " . count($notifications) . " unread notifications</p>";
        
        // Try adding a test notification
        addNotification($admin['id'], 'Test Notification', 'This is a test message from db_test.php', 'info');
        echo "<p class='success'>✅ Test notification added</p>";
        
        // Check again
        $notifications = getUserNotifications($admin['id']);
        echo "<p>Admin now has " . count($notifications) . " unread notifications</p>";
    }
    echo "</div>";
    
    // Summary
    echo "<div class='section'>";
    echo "<h2>Summary</h2>";
    if ($db) {
        echo "<p class='success'>✅ Database is working correctly!</p>";
        echo "<p>You can now use the application with database features.</p>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Database Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<p><a href='index.php'>Go to Login Page</a> | <a href='admin-dashboard.php'>Go to Admin Dashboard</a></p>";
echo "</body></html>";
?>