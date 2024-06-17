<?php
session_start();

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to get all posts
function getAllPosts($connection) {
    $sql = "SELECT p.*, t.name AS tag_name 
            FROM posts p
            LEFT JOIN tags t ON p.tags_id = t.id 
            ORDER BY p.created_at DESC"; // Join with tags table to get tag name
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return false;
    }
}

// Get all posts
$posts = getAllPosts($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Social</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .file-preview {
            max-width: 100%;
            height: 150px;
            object-fit: contain;
        }

        .media-item {
            position: relative;
            margin: 5px;
        }

        .media-item .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #f00;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .media-controls {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        #media {
            display: none;
        }

        #uploadLabel {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        #tags {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            width: 100%;
            background-color: #fff;
            color: #333;
            appearance: none;
            -webkit-appearance: none;
        }

        #tags option {
            padding: 5px;
            background-color: #fff;
            color: #333;
        }

        #tags:focus {
            outline: none;
            border-color: #007bff;
        }

        #tags::-ms-expand {
            display: none;
        }

        #tags::after {
            content: '▼';
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .seethrough {
            background-color: rgba(248, 249, 250, 0.9);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-sm-5 p-5 rounded-lg seethrough">
        <?php if (isset($_SESSION["username"])): ?>
            <div class="create-post-container">
                <h2>Create a New Post</h2>
                <form action="create_post.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Post Title:</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content:</label>
                        <textarea name="content" id="content" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="media" class="form-label">Media (Max 3 files):</label>
                        <label id="uploadLabel" for="media">Choose files</label>
                        <input type="file" name="media[]" id="media" multiple accept="image/*,video/*" />
                        <div id="filePreviews" class="mt-3"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags_id" class="form-label">Tag:</label>
                        <select name="tags_id" id="tags" class="form-select">
                            <?php
                            $tagsQuery = "SELECT * FROM tags";
                            $tagsResult = mysqli_query($connection, $tagsQuery);

                            while ($tag = mysqli_fetch_assoc($tagsResult)) {
                                echo "<option value='" . $tag['id'] . "'>" . $tag['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Post</button>
                </form>
            </div>
        <?php else : ?>
            <div class="alert alert-warning" role="alert">
                To create posts, please <a href="login.php" class="alert-link">log in</a> or <a href="register.php" class="alert-link">register</a>.
            </div>
        <?php endif; ?>

        <h2 class="mt-5">Current posts</h2>
        <?php if ($posts) : ?>
            <div class="post-list">
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-3 post-box" data-post-id="<?php echo $post['id']; ?>">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars_decode($post['title']); ?></h3>
                            <p class="card-text">Created by: <span class="post-author"><?php echo $post['created_by']; ?></span> on <span class="post-date"><?php echo $post['created_at']; ?></span> - <span class="post-tags"><?php echo $post['tag_name'] ?? 'No tag'; ?></span></p>
                            <p class="card-text"><?php echo htmlspecialchars_decode($post['content']); ?></p>
                            <div class="post-media" data-current-media-index="0">
                                <?php 
                                $mediaFiles = explode(',', $post['media']);
                                foreach($mediaFiles as $index => $mediaFile) : 
                                    $filePath = "content/" . $mediaFile;
                                    $fileType = mime_content_type($filePath);
                                    $display = ($index === 0) ? 'block' : 'none'; // Display the first media item by default
                                    ?>
                                    <div class="media-item" style="display: <?= $display ?>;">
                                        <?php if (strpos($fileType, 'image/') === 0) : ?>
                                            <img src="<?= $filePath ?>" alt="Post Media" class="file-preview">
                                        <?php elseif (strpos($fileType, 'video/') === 0) : ?>
                                            <video src="<?= $filePath ?>" controls class="file-preview"></video>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                <div class="media-controls" style="display: <?php echo (count($mediaFiles) > 1) ? 'flex' : 'none'; ?>;">
                                    <button class="btn btn-outline-primary me-2" onclick="showPrevMedia(<?php echo $post['id']; ?>)">&lt;</button>
                                    <button class="btn btn-outline-primary" onclick="showNextMedia(<?php echo $post['id']; ?>)">&gt;</button>
                                </div>
                            </div>
                            <a href="view_post.php?id=<?php echo $post['id']; ?>" class="btn btn-link mt-3">Read Comments</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No posts created yet.</p>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('media');
            const previewsContainer = document.getElementById('filePreviews');

            fileInput.addEventListener('change', () => {
                handleFiles(fileInput.files);
            });

            // Handle Files Function
            function handleFiles(files) {
                previewsContainer.innerHTML = ''; // Clear existing previews
                for (let i = 0; i < Math.min(files.length, 3); i++) {
                    const file = files[i];

                    const mediaItem = document.createElement('div');
                    mediaItem.classList.add('media-item');

                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.classList.add('file-preview');
                        img.file = file;
                        mediaItem.appendChild(img);

                        const reader = new FileReader();
                        reader.onload = (function (aImg) {
                            return function (e) {
                                aImg.src = e.target.result;
                            };
                        })(img);
                        reader.readAsDataURL(file);
                    } else if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.classList.add('file-preview');
                        video.controls = true;
                        video.file = file;
                        mediaItem.appendChild(video);

                        const reader = new FileReader();
                        reader.onload = (function (aVideo) {
                            return function (e) {
                                aVideo.src = e.target.result;
                            };
                        })(video);
                        reader.readAsDataURL(file);
                    }

                    const removeButton = document.createElement('button');
                    removeButton.classList.add('remove-btn');
                    removeButton.textContent = 'Remove';
                    removeButton.onclick = () => {
                        mediaItem.remove();
                        fileInput.value = '';
                    };

                    mediaItem.appendChild(removeButton);
                    previewsContainer.appendChild(mediaItem);
                }
            }
        });

        function showNextMedia(postId) {
            const postBox = document.querySelector(`.post-box[data-post-id="${postId}"]`);
            const mediaItems = postBox.querySelectorAll('.media-item');
            const currentIndex = parseInt(postBox.querySelector('.post-media').getAttribute('data-current-media-index'));

            mediaItems[currentIndex].style.display = 'none';
            const nextIndex = (currentIndex + 1) % mediaItems.length;
            mediaItems[nextIndex].style.display = 'block';
            postBox.querySelector('.post-media').setAttribute('data-current-media-index', nextIndex);
        }

        function showPrevMedia(postId) {
            const postBox = document.querySelector(`.post-box[data-post-id="${postId}"]`);
            const mediaItems = postBox.querySelectorAll('.media-item');
            const currentIndex = parseInt(postBox.querySelector('.post-media').getAttribute('data-current-media-index'));

            mediaItems[currentIndex].style.display = 'none';
            const prevIndex = (currentIndex - 1 + mediaItems.length) % mediaItems.length;
            mediaItems[prevIndex].style.display = 'block';
            postBox.querySelector('.post-media').setAttribute('data-current-media-index', prevIndex);
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
