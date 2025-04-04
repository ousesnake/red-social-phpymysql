<?php
session_start();
require_once "config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $content = trim($_POST["content"]);
    $user_id = $_SESSION["id"];
    $image = null;
    
    // Handle image upload
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $target_dir = "uploads/";
        
        // Create uploads directory if it doesn't exist
        if(!file_exists($target_dir)){
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $image_name = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $image_name;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false){
            // Check file size (5MB max)
            if($_FILES["image"]["size"] <= 5000000){
                // Allow certain file formats
                if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif"){
                    if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
                        $image = $image_name;
                    }
                }
            }
        }
    }
    
    // Insert post into database
    $sql = "INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $content, $image);
        
        if(mysqli_stmt_execute($stmt)){
            header("location: index.php");
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
}
?> 