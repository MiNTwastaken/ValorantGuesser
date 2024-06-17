<?php
// Start output buffering
ob_start();

session_start();
include 'navbar.php';

// Security Check
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== 1) {
    header("Location: login.php");
    exit;
}

// Database Connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle User Management Actions and Set Session Messages
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["make_admin"])) {
        $username = $_POST["make_admin"];
        $sql = "UPDATE user SET admin = 1 WHERE username = '$username'";
        if (mysqli_query($connection, $sql)) {
            $_SESSION['success_message'] = "User '$username' has been made an admin.";
        } else {
            $_SESSION['error_message'] = "Error making user admin: " . mysqli_error($connection);
        }
    } elseif (isset($_POST["delete_user"])) {
        $username = $_POST["delete_user"];
        
        // Manually delete related comments first
        $delete_comments_sql = "DELETE FROM comments WHERE commenter = '$username'";
        if (mysqli_query($connection, $delete_comments_sql)) {
            $sql = "DELETE FROM user WHERE username = '$username'";
            if (mysqli_query($connection, $sql)) {
                $_SESSION['success_message'] = "User '$username' has been deleted.";
            } else {
                $_SESSION['error_message'] = "Error deleting user: " . mysqli_error($connection);
            }
        } else {
            $_SESSION['error_message'] = "Error deleting user's comments: " . mysqli_error($connection);
        }
    }

    // Redirect to avoid duplicate messages on refresh
    header("Location: admin.php");
    exit;
}

// Fetch User Data for Display
$sql = "SELECT username, email, picture, favorite, lvl, exp, admin FROM user";
$result = mysqli_query($connection, $sql);

$users = [];
if (mysqli_num_rows($result) > 0) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// End output buffering and flush output
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Fanpage Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .seethrough{
            background-color: rgba(248, 249, 250, 0.9);
        }
        .img-thumbnail {
            width: 100%;
            height: 20rem; 
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-sm-5 p-5 rounded-lg seethrough">
        <h1 class="mb-4">Valorant Fanpage Admin Panel</h1>
        <p>Welcome, <?php echo $_SESSION["username"]; ?>. Manage everything here</p>

        <?php 
        // Display session messages
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']); // Clear the message
        }

        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Clear the message
        }
        ?>

        <form method="post" action="show_data.php" class="mb-4">
            <div class="form-group">
                <label for="data_type">Select Data Type:</label>
                <select name="data_type" id="data_type" class="form-control">
                    <option value="ability">Abilities</option>
                    <option value="agent">Agents</option>
                    <option value="graffiti">Graffiti</option>
                    <option value="playercard">Player Cards</option>
                    <option value="quote">Quotes</option>
                    <option value="weapon">Weapons</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Show Data</button>
        </form>

        <a href="gamedata.php" class="btn btn-secondary mb-4">Fetch information from API</a>
        <a href="posts.php" class="btn btn-secondary mb-4">Manage posts</a>

        <h2>User Management</h2>
        <div class="row">
            <?php foreach ($users as $user) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <img src="<?php echo $user['picture']; ?>" alt="Profile Picture" class="img-thumbnail mb-3">
                            </div>
                            <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                            <p><strong>Favorite:</strong> <?php echo $user['favorite']; ?></p>
                            <p><strong>Level:</strong> <?php echo $user['lvl']; ?></p>
                            <p><strong>Experience:</strong> <?php echo $user['exp']; ?></p>
                            <p><strong>Admin:</strong> <?php echo $user['admin'] ? 'Yes' : 'No'; ?></p>

                            <div class="d-flex justify-content-between mt-3">
                                <?php if (!$user['admin']) : ?>
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <input type="hidden" name="make_admin" value="<?php echo $user['username']; ?>">
                                        <button type="submit" class="btn btn-success">Make Admin</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="delete_user" value="<?php echo $user['username']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div> 
    </div>
</body>
</html>
