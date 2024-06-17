<?php
session_start();

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get user information based on username from session
$username = $_SESSION["username"];
$sql = "SELECT u.*, l.name AS level_name, l.total_exp AS next_level_exp 
        FROM user u 
        JOIN levels l ON u.lvl = l.id 
        WHERE username = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);

    // Format joined_at for display
    $joinedAtFormatted = date("F j, Y", strtotime($user['joined_at']));
} else {
    // Handle case where user is not found
    header("Location: login.php"); // Or display an error message
    exit();
}
mysqli_stmt_close($stmt);

// Handle edit mode using a hidden form field
$editing = isset($_POST['edit_mode']) && $_POST['edit_mode'] === 'true';

// Update Profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $editing = false; // Exit edit mode after submitting

    // Handle profile picture upload
    $newPicture = $user['picture']; // Initialize with existing picture
    if (!empty($_FILES["picture"]["name"])) {
        $targetDir = "img/profile/";
        $targetFile = $targetDir . basename($_FILES["picture"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["picture"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                $newPicture = $targetFile; // Update with new picture path
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
    
    // Sanitize form data
    $newFavorite = mysqli_real_escape_string($connection, $_POST["favorite"]);
    $newEmail = mysqli_real_escape_string($connection, $_POST["email"]);
    $newPassword = $_POST["password"]; // Get the new password

    // Check if the new password is provided
    if (!empty($newPassword)) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    } else {
        // Use the existing password if no new password is provided
        $hashedPassword = $user['password'];
    }

    // Prepare and execute the SQL update statement (without username update)
    $stmt = mysqli_prepare($connection, "UPDATE user SET picture = ?, favorite = ?, email = ?, password = ? WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "sssss", $newPicture, $newFavorite, $newEmail, $hashedPassword, $username); 

    if (mysqli_stmt_execute($stmt)) {
        // Update session email
        $_SESSION['email'] = $newEmail;

        // After updating, redirect to profile.php
        header("Location: profile.php");
        exit(); 
    } else {
        echo "Error updating record: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .profile-container {
            max-width: 800px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
        }
        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .profile-picture {
            margin-bottom: 1rem;
        }
        .profile-picture img {
            width: 10rem;
            height: 10rem;
            border-radius: 50%;
            object-fit: cover;
            border:0.3rem solid #FF4654;
        }
        .user-details ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .user-details li {
            margin-bottom: 0.5rem;
        }
        .exp-bar {
            background-color: #ddd;
            border-radius: 0.5rem;
            overflow: hidden;
            width: 100%;
            height: 1rem;
            margin: 1rem 0;
        }
        .exp-bar div {
            background-color: #4caf50;
            height: 100%;
        }
        button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            background-color: #007bff;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        input[type="email"], input[type="file"], input[type="password"], select {
            padding: 0.5rem;
            width: 100%;
            max-width: 20rem;
            margin: 0.5rem 0;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
        }
        input[type="checkbox"] {
            margin-left: 0.5rem;
        }
    </style>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="profile-container mt-sm-5 p-5">
        <h2>Profile</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="edit_mode" value="<?php echo $editing ? 'true' : 'false'; ?>">
            <div class="profile-info">
                <div class="profile-picture">
                    <img src="<?php echo $user['picture']; ?>" alt="Profile Picture">
                    <?php if ($editing): ?>
                        <input type="file" name="picture" id="picture">
                    <?php endif; ?>
                </div>
                <div class="user-details">
                    <ul>
                        <li>Username: <?php echo htmlspecialchars($user['username']); ?></li>
                        <li>Email:
                            <?php if ($editing): ?>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                            <?php else: echo htmlspecialchars($user['email']); endif; ?>
                        </li>
                        <li>Favorite Agent:
                            <?php if ($editing): ?>
                                <select name="favorite" id="favoriteAgent"></select>
                            <?php else: echo htmlspecialchars($user['favorite']); endif; ?>
                        </li>
                        <li>Level: <?php echo htmlspecialchars($user['level_name']); ?></li>
                        <li>EXP: <?php echo $user['exp']; ?> / <?php echo $user['next_level_exp']; ?> (Next Level)</li>
                        <div class="exp-bar">
                            <div style="width: <?php echo ($user['exp'] / $user['next_level_exp']) * 100; ?>%;"></div>
                        </div>
                        <li>Joined at: <?php echo $joinedAtFormatted; ?></li>
                        <?php if ($editing): ?>
                            <li>Password:
                                <input type="password" name="password" id="password">
                                <input type="checkbox" onclick="togglePasswordVisibility()"> Show Password
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php if (!$editing): ?>
                <button type="submit" name="edit_mode" value="true">Edit</button>
            <?php else: ?>
                <button type="submit" name="submit">Update Profile</button>
            <?php endif; ?>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const favoriteAgentSelect = document.getElementById('favoriteAgent');

        // Fetch agent data from the API
        fetch('https://valorant-api.com/v1/agents')
            .then(response => response.json())
            .then(data => {
                data.data.forEach(agent => {
                    if (agent.isPlayableCharacter) {
                        const option = document.createElement('option');
                        option.value = agent.displayName;
                        option.text = agent.displayName;
                        // Set the selected attribute if it matches the user's favorite agent
                        if (agent.displayName === "<?php echo $user['favorite']; ?>") {
                            option.selected = true;
                        }
                        favoriteAgentSelect.add(option);
                    }
                });
            });
    });
    </script>
</body>
</html>
