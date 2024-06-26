<?php
session_start();
ob_start(); // Start output buffering

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || (isset($_SESSION["admin"]) && $_SESSION["admin"] != 1)) {
    header("Location: login.php");
    exit();
}

ini_set('max_execution_time', 1000); // Increase max execution time 

// Database connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");

// Function to fetch and update game data
function updateGameData($category, $endpoint) {
    global $connection;

    $response = file_get_contents("https://valorant-api.com/v1/$endpoint");
    $data = json_decode($response, true);

    // Mapping category numbers to their respective folder names
    $categoryFolders = [
        1 => 'agents',
        2 => 'weapons',
        3 => 'sprays',
        4 => 'playercards',
        5 => 'buddies',
        6 => 'skins' // Removed category 7
    ];

    if ($data['status'] == 200) {
        // Create directories if they don't exist
        $categoryFolder = "img/" . $categoryFolders[$category];
        if (!file_exists($categoryFolder)) {
            mkdir($categoryFolder, 0777, true);
        }

        // 1. Track Existing Files:
        $existingFiles = [];
        if (file_exists($categoryFolder)) {
            $existingFiles = scandir($categoryFolder);
        }

        // Prepare the insert statement outside the loop
        $stmt = mysqli_prepare($connection, "INSERT INTO gamedata (category, name, img) VALUES (?, ?, ?)");

        foreach ($data['data'] as $item) {
            $sanitizedName = preg_replace('/[\\\\\/:*?"<>|]/u', '_', $item['displayName']);
            $imagePath = $categoryFolder . "/" . $sanitizedName . ".png";

            // Display a message indicating the current item being processed

            // Prioritized check for displayIcon sources:
            $image_url = null; // Initialize to null
            if (!empty($item['displayIcon'])) { // 1. Top-level displayIcon
                $image_url = $item['displayIcon'];
            } elseif (isset($item['levels']) && is_array($item['levels'])) { // 2. Levels (for weapon skins)
                foreach ($item['levels'] as $level) {
                    if (!empty($level['displayIcon'])) {
                        $image_url = $level['displayIcon'];
                        break;
                    }
                }
            } elseif (isset($item['chromas']) && is_array($item['chromas'])) { // 3. Chromas (for weapon skins)
                foreach ($item['chromas'] as $chroma) {
                    if (!empty($chroma['displayIcon'])) {
                        $image_url = $chroma['displayIcon'];
                        break;
                    }
                }
            }

            if ($image_url) { 
                // 2. Compare with API Data:
                if (!in_array($sanitizedName . ".png", $existingFiles)) {
                    // 3. Download Missing Files:
                    file_put_contents($imagePath, file_get_contents($image_url));
                }

                // Check if entry exists in the database
                $checkStmt = mysqli_prepare($connection, "SELECT id FROM gamedata WHERE category = ? AND name = ?");
                mysqli_stmt_bind_param($checkStmt, "is", $category, $item['displayName']);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);

                if (mysqli_stmt_num_rows($checkStmt) == 0) { // Insert only if it doesn't exist
                    mysqli_stmt_bind_param($stmt, "iss", $category, $item['displayName'], $imagePath);
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "Error inserting data for " . $item['displayName'] . ": " . mysqli_error($connection) . "<br>";
                    }
                }

                mysqli_stmt_close($checkStmt);
            } else {
                echo "Warning: No displayIcon found for " . $item['displayName'] . "<br>";
            }
        }

        mysqli_stmt_close($stmt); // Close the final prepared statement

    } else {
        echo "Error fetching data from the API for $category.<br>";
    }
}

function addRandomDailyChallenge() {
    global $connection;

    // Get unique categories (you might already have this in your existing code)
    $categoriesResult = mysqli_query($connection, "SELECT DISTINCT category FROM gamedata");

    // TRUNCATE dailychallenge table before inserting new challenges
    mysqli_query($connection, "TRUNCATE TABLE dailychallenge"); 

    // Prepare the insert statement
    $stmt = mysqli_prepare($connection, "INSERT INTO dailychallenge (gamedata_id) VALUES (?)");

    while ($category = mysqli_fetch_assoc($categoriesResult)) {
        $categoryId = $category['category'];

        // Get a random gamedata_id for the category
        $randomGameDataResult = mysqli_query($connection, "SELECT id FROM gamedata WHERE category = $categoryId ORDER BY RAND() LIMIT 1");
        
        // Check if any results were found
        if (mysqli_num_rows($randomGameDataResult) > 0) {
            $randomGameDataId = mysqli_fetch_assoc($randomGameDataResult)['id'];

            // Insert the gamedata_id into dailychallenge
            mysqli_stmt_bind_param($stmt, "i", $randomGameDataId);
            mysqli_stmt_execute($stmt);
        } else {
            // Handle the case where no game data exists for the category
            echo "Warning: No game data found for category $categoryId.<br>";
        }
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamedata Management Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .btn-custom {
            width: 100%;
            margin-bottom: 10px;
        }
        .alert {
            margin-top: 20px;
        }
        .seethrough{
            background-color: rgba(248, 249, 250, 0.9);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="card shadow-sm p-4 mt-sm-5 seethrough">
            <h2 class="mb-4">Gamedata Management Panel</h2>
            <form method="post" action="">
                <button type="submit" name="update_data" class="btn btn-primary btn-custom">Update Game Data</button>
            </form>
            <form method="post" action="">
                <button type="submit" name="add_daily_challenge" class="btn btn-secondary btn-custom">Add Random Daily Challenge</button>
            </form>
            <div id="update-status">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST['update_data'])) {
                        updateGameData(1, 'agents'); 
                        updateGameData(2, 'weapons');
                        updateGameData(3, 'sprays'); 
                        updateGameData(4, 'playercards'); 
                        updateGameData(5, 'buddies'); 
                        updateGameData(6, 'weapons/skins'); 

                        // Redirect after successful update
                        header("Location: gamedata.php?success=1"); // Redirect with success message
                        exit;
                    } 
                    if (isset($_POST['add_daily_challenge'])) { 
                        addRandomDailyChallenge();
                        echo "<div class='alert alert-success'>Daily challenges added successfully!</div>";
                        header("Refresh: 2; url=gamedata.php"); // Refresh after 3 seconds
                        exit; // Stop further execution
                    }
                }

                // Display success message if redirected from update
                if (isset($_GET['success']) && $_GET['success'] == 1) {
                    echo "<div class='alert alert-success'>Game data updated successfully!</div>";
                }
                ?>
            </div>
        </div>
    </div>
    <script>
        if (window.location.href.indexOf('success=1') > -1) {
            setTimeout(function() {
                window.location.href = "gamedata.php"; // Redirect to gamedata.php
            }, 2000);
        }
    </script>
</body>
</html>

<?php
ob_end_flush(); // End output buffering and flush the buffer
?>
