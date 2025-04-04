<?php
session_start();
require_once "config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"]) && isset($_POST["content"])){
    $user_id = $_SESSION["id"];
    $post_id = $_POST["post_id"];
    $content = trim($_POST["content"]);
    
    if(empty($content)){
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Comment cannot be empty']);
        exit;
    }
    
    // Insert comment into database
    $sql = "INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $post_id, $content);
        
        if(mysqli_stmt_execute($stmt)){
            // Get the inserted comment with user info
            $comment_id = mysqli_insert_id($conn);
            $get_comment_sql = "SELECT c.*, u.username, u.profile_picture 
                              FROM comments c 
                              JOIN users u ON c.user_id = u.id 
                              WHERE c.id = ?";
            
            $get_stmt = mysqli_prepare($conn, $get_comment_sql);
            mysqli_stmt_bind_param($get_stmt, "i", $comment_id);
            mysqli_stmt_execute($get_stmt);
            $result = mysqli_stmt_get_result($get_stmt);
            $comment = mysqli_fetch_assoc($result);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'comment' => [
                    'id' => $comment['id'],
                    'content' => $comment['content'],
                    'username' => $comment['username'],
                    'profile_picture' => $comment['profile_picture'],
                    'created_at' => $comment['created_at']
                ]
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