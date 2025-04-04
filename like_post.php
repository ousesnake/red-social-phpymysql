<?php
session_start();
require_once "config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"])){
    $user_id = $_SESSION["id"];
    $post_id = $_POST["post_id"];
    
    // Check if user already liked the post
    $check_sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $post_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if(mysqli_stmt_num_rows($check_stmt) > 0){
        // Unlike the post
        $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        $action = 'unliked';
    } else {
        // Like the post
        $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        $action = 'liked';
    }
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $post_id);
        
        if(mysqli_stmt_execute($stmt)){
            // Get updated like count
            $count_sql = "SELECT COUNT(*) as count FROM likes WHERE post_id = ?";
            $count_stmt = mysqli_prepare($conn, $count_sql);
            mysqli_stmt_bind_param($count_stmt, "i", $post_id);
            mysqli_stmt_execute($count_stmt);
            $result = mysqli_stmt_get_result($count_stmt);
            $row = mysqli_fetch_assoc($result);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'action' => $action,
                'count' => $row['count']
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error']);
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request']);
}
?> 