<?php
session_start();
require_once "config.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get user ID from URL or use logged-in user's ID
$profile_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id'];

// Get user information
$user_sql = "SELECT id, username, email, profile_picture, created_at FROM users WHERE id = ?";
$user_stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($user_stmt, "i", $profile_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user = mysqli_fetch_assoc($user_result);

if(!$user){
    header("location: index.php");
    exit;
}

// Get user's posts
$posts_sql = "SELECT p.*, u.username, u.profile_picture,
              (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
              (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              WHERE p.user_id = ?
              ORDER BY p.created_at DESC";
$posts_stmt = mysqli_prepare($conn, $posts_sql);
mysqli_stmt_bind_param($posts_stmt, "i", $profile_id);
mysqli_stmt_execute($posts_stmt);
$posts_result = mysqli_stmt_get_result($posts_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user["username"]); ?>'s Profile - Social Network</title>
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
        .profile-header {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .post-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
            margin-bottom: 20px;
        }
        .small-profile-picture {
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION["profile_picture"]); ?>" 
                                 class="small-profile-picture me-2" alt="Profile">
                            <?php echo htmlspecialchars($_SESSION["username"]); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="<?php echo htmlspecialchars($user["profile_picture"]); ?>" 
                         class="profile-picture mb-3" alt="Profile Picture">
                </div>
                <div class="col-md-9">
                    <h2><?php echo htmlspecialchars($user["username"]); ?></h2>
                    <p class="text-muted">Member since <?php echo date('F Y', strtotime($user["created_at"])); ?></p>
                    <?php if($profile_id == $_SESSION["id"]): ?>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            Edit Profile
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <?php if($profile_id == $_SESSION["id"]): ?>
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
                <?php endif; ?>

                <!-- Posts -->
                <?php if(mysqli_num_rows($posts_result) > 0): ?>
                    <?php while($post = mysqli_fetch_assoc($posts_result)): ?>
                        <div class="post-card p-3">
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?php echo htmlspecialchars($post["profile_picture"]); ?>" 
                                     class="small-profile-picture me-2" alt="Profile">
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
                                    <button class="btn btn-sm btn-outline-primary me-2 like-button" 
                                            data-post-id="<?php echo $post["id"]; ?>">
                                        <i class="far fa-heart"></i> Like (<?php echo $post["like_count"]; ?>)
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary comment-button" 
                                            data-post-id="<?php echo $post["id"]; ?>">
                                        <i class="far fa-comment"></i> Comment (<?php echo $post["comment_count"]; ?>)
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Comments Section -->
                            <div class="comments-section mt-3" id="comments-<?php echo $post["id"]; ?>" style="display: none;">
                                <div class="comments-list mb-3">
                                    <!-- Comments will be loaded here -->
                                </div>
                                <form class="comment-form" data-post-id="<?php echo $post["id"]; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Write a comment...">
                                        <button class="btn btn-primary" type="submit">Post</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center">
                        <p>No posts yet.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <!-- Additional profile information or widgets can go here -->
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <?php if($profile_id == $_SESSION["id"]): ?>
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="update_profile.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" name="profile_picture" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($user["email"]); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Like button functionality
            $('.like-button').click(function() {
                const postId = $(this).data('post-id');
                const button = $(this);
                
                $.post('like_post.php', {post_id: postId}, function(response) {
                    if(response.success) {
                        const count = response.count;
                        button.html(`<i class="far fa-heart"></i> Like (${count})`);
                    }
                });
            });

            // Comment button functionality
            $('.comment-button').click(function() {
                const postId = $(this).data('post-id');
                const commentsSection = $(`#comments-${postId}`);
                
                if(commentsSection.is(':visible')) {
                    commentsSection.hide();
                } else {
                    commentsSection.show();
                    loadComments(postId);
                }
            });

            // Comment form submission
            $('.comment-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const postId = form.data('post-id');
                const input = form.find('input');
                const content = input.val();

                if(content.trim() !== '') {
                    $.post('add_comment.php', {
                        post_id: postId,
                        content: content
                    }, function(response) {
                        if(response.success) {
                            input.val('');
                            loadComments(postId);
                        }
                    });
                }
            });

            function loadComments(postId) {
                $.get('get_comments.php', {post_id: postId}, function(response) {
                    const commentsList = $(`#comments-${postId} .comments-list`);
                    commentsList.empty();

                    response.comments.forEach(comment => {
                        commentsList.append(`
                            <div class="d-flex mb-2">
                                <img src="${comment.profile_picture}" class="small-profile-picture me-2" alt="Profile">
                                <div class="flex-grow-1">
                                    <strong>${comment.username}</strong>
                                    <p class="mb-0">${comment.content}</p>
                                    <small class="text-muted">${comment.created_at}</small>
                                </div>
                            </div>
                        `);
                    });
                });
            }
        });
    </script>
</body>
</html> 