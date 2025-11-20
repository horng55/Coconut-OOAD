<?php
// Test MySQL connection with different credentials using MySQLi
$host = '127.0.0.1';
$port = 3306;
$dbname = 'school_management';

$credentials = [
    ['root', ''],
    ['root', 'root'],
    ['root', 'password'],
];

echo "Testing MySQL connection...\n\n";

foreach ($credentials as $cred) {
    [$username, $password] = $cred;
    
    // Try with mysqli
    $conn = @mysqli_connect($host, $username, $password, '', $port);
    
    if ($conn) {
        echo "✓ MySQLi connection successful with username: $username, password: " . ($password ? "'$password'" : "(empty)") . "\n";
        
        // Try to create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (mysqli_query($conn, $sql)) {
            echo "✓ Database '$dbname' created or already exists\n";
        } else {
            echo "! Database creation: " . mysqli_error($conn) . "\n";
        }
        
        echo "\nUse these credentials in your .env file:\n";
        echo "DB_USERNAME=root\n";
        echo "DB_PASSWORD=" . $password . "\n";
        
        mysqli_close($conn);
        exit(0);
    } else {
        echo "✗ MySQLi failed with username: $username, password: " . ($password ? "'$password'" : "(empty)") . " - " . mysqli_connect_error() . "\n";
    }
}

echo "\n❌ Could not connect with any credentials. Please check your XAMPP MySQL configuration.\n";
echo "\nPlease ensure:\n";
echo "1. XAMPP MySQL is running\n";
echo "2. Check MySQL error logs at: C:\\xampp\\mysql\\data\\*.err\n";
echo "3. Try accessing phpMyAdmin at http://localhost/phpmyadmin\n";
