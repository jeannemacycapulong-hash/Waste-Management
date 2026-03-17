<?php
// auto_restore.php - Automatically restore if database doesn't exist

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'wastemanager';
$backup_file = 'wastemanager.sql';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    $exists = $stmt->fetch() !== false;
    
    if (!$exists) {
        echo "Database missing. Restoring from backup...\n";
        
        // Create database
        $pdo->exec("CREATE DATABASE `$dbname`");
        $pdo->exec("USE `$dbname`");
        
        // Import backup
        $sql = file_get_contents($backup_file);
        $pdo->exec($sql);
        
        echo "Database restored successfully!\n";
    } else {
        echo "Database already exists. No action taken.\n";
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>