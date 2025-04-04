<?php
session_start();
require_once "config.php";

// Check if user is logged in
$loggedin = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .post-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
            margin-bottom: 20px;
        }
        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Social Network</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if($loggedin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <img src="<?php echo htmlspecialchars($_SESSION["profile_picture"]); ?>" 
                                     class="profile-picture me-2" alt="Profile">
                                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if($loggedin): ?>
            <!-- Create Post Form -->
            <div class="post-card p-3 mb-4">
                <form action="create_post.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" placeholder="What's on your mind?"></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <label class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-image"></i> Add Photo
                                <input type="file" name="image" style="display: none;">
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </div>
                </form>
            </div>

            <!-- Posts Feed -->
            <?php
            $sql = "SELECT p.*, u.username, u.profile_picture, 
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
                    FROM posts p 
                    JOIN users u ON p.user_id = u.id 
                    ORDER BY p.created_at DESC";
            $result = mysqli_query($conn, $sql);

            if(mysqli_num_rows($result) > 0):
                while($post = mysqli_fetch_assoc($result)):
            ?>
                <div class="post-card p-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo htmlspecialchars($post["profile_picture"]); ?>" 
                             class="profile-picture me-2" alt="Profile">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($post["username"]); ?></h6>
                            <small class="text-muted">
                                <?php echo date('F j, Y, g:i a', strtotime($post["created_at"])); ?>
                            </small>
                        </div>
                    </div>
                    <p class="mb-3"><?php echo nl2br(htmlspecialchars($post["content"])); ?></p>
                    <?php if($post["image"]): ?>
                        <img src="uploads/<?php echo htmlspecialchars($post["image"]); ?>" 
                             class="img-fluid rounded mb-3" alt="Post image">
                    <?php endif; ?>
                    <div class="d-flex justify-content-between">
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-2">
                                <i class="far fa-heart"></i> Like (<?php echo $post["like_count"]; ?>)
                            </button>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="far fa-comment"></i> Comment (<?php echo $post["comment_count"]; ?>)
                            </button>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="text-center">
                    <p>No posts yet. Be the first to share something!</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center">
                <h1 class="display-4 mb-4">Welcome to Social Network</h1>
                <p class="lead">Join our community and connect with friends!</p>
                <div class="mt-4">
                    <a href="login.php" class="btn btn-primary btn-lg me-3">Login</a>
                    <a href="register.php" class="btn btn-outline-primary btn-lg">Register</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 