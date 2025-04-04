<?php
session_start();
require_once "config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

if(isset($_GET["post_id"])){
    $post_id = $_GET["post_id"];
    
    // Get comments for the post
    $sql = "SELECT c.*, u.username, u.profile_picture 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = ? 
            ORDER BY c.created_at DESC";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $post_id);
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            $comments = [];
            
            while($row = mysqli_fetch_assoc($result)){
                $comments[] = [
                    'id' => $row['id'],
                    'content' => $row['content'],
                    'username' => $row['username'],
                    'profile_picture' => $row['profile_picture'],
                    'created_at' => date('F j, Y, g:i a', strtotime($row['created_at']))
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'comments' => $comments]);
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