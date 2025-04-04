<?php
session_start();
require_once "config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user_id = $_SESSION["id"];
    $email = trim($_POST["email"]);
    $profile_picture = $_SESSION["profile_picture"]; // Keep existing picture by default
    
    // Handle profile picture upload
    if(isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0){
        $target_dir = "uploads/";
        
        // Create uploads directory if it doesn't exist
        if(!file_exists($target_dir)){
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $image_name = "profile_" . $user_id . "_" . uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $image_name;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if($check !== false){
            // Check file size (5MB max)
            if($_FILES["profile_picture"]["size"] <= 5000000){
                // Allow certain file formats
                if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif"){
                    if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)){
                        // Delete old profile picture if it exists and is not the default
                        if($_SESSION["profile_picture"] != "default.jpg" && file_exists($target_dir . $_SESSION["profile_picture"])){
                            unlink($target_dir . $_SESSION["profile_picture"]);
                        }
                        $profile_picture = $image_name;
                    }
                }
            }
        }
    }
    
    // Update user information in database
    $sql = "UPDATE users SET email = ?, profile_picture = ? WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssi", $email, $profile_picture, $user_id);
        
        if(mysqli_stmt_execute($stmt)){
            $_SESSION["profile_picture"] = $profile_picture;
            header("location: profile.php");
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
}
?> 