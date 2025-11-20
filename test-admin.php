<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$app->boot();

echo "Testing Admin Dashboard...\n";

try {
    // Test database connection
    $pdo = new PDO('sqlite:database/database.sqlite');
    echo "✓ Database connection works\n";
    
    // Test if admin user exists
    $stmt = $pdo->query("SELECT * FROM users WHERE type = 'admin' LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        echo "✓ Admin user exists: " . $admin['email'] . "\n";
    } else {
        echo "✗ No admin user found\n";
    }
    
    // Test if required tables exist
    $tables = ['users', 'teachers', 'students', 'classes', 'enrollments', 'subjects'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "✓ Table '$table' exists with $count records\n";
        } catch (Exception $e) {
            echo "✗ Table '$table' missing or error: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}