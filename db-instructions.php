<?php
echo "Please create the database manually:\n\n";
echo "1. Open phpMyAdmin at: http://localhost/phpmyadmin\n";
echo "2. Click on 'New' in the left sidebar\n";
echo "3. Database name: school_management\n";
echo "4. Collation: utf8mb4_unicode_ci\n";
echo "5. Click 'Create'\n\n";
echo "Or run this SQL in phpMyAdmin SQL tab:\n";
echo "CREATE DATABASE IF NOT EXISTS school_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n\n";
echo "Then update your .env file with the correct DB_PASSWORD that works in phpMyAdmin.\n";
