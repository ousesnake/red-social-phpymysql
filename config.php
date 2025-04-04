<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'social_network');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if(mysqli_query($conn, $sql)){
    mysqli_select_db($conn, DB_NAME);
} else {
    die("ERROR: Could not create database. " . mysqli_error($conn));
}

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    profile_picture VARCHAR(255) DEFAULT 'default.jpg',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if(!mysqli_query($conn, $sql)){
    die("ERROR: Could not create users table. " . mysqli_error($conn));
}

// Create posts table
$sql = "CREATE TABLE IF NOT EXISTS posts (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if(!mysqli_query($conn, $sql)){
    die("ERROR: Could not create posts table. " . mysqli_error($conn));
}

// Create likes table
$sql = "CREATE TABLE IF NOT EXISTS likes (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (user_id, post_id)
)";

if(!mysqli_query($conn, $sql)){
    die("ERROR: Could not create likes table. " . mysqli_error($conn));
}

// Create comments table
$sql = "CREATE TABLE IF NOT EXISTS comments (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
)";

if(!mysqli_query($conn, $sql)){
    die("ERROR: Could not create comments table. " . mysqli_error($conn));
}
?> 