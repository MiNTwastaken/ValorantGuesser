<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php"); // Redirect to login page if not authorized
    exit();
}

// Database Connection
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "valorantfanpage";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch a random challenge based on the chosen category
function getChallenge($category = 1) { // Default to category 1 (Agents)
    global $conn;
    $sql = "SELECT gd.name, gd.img, gd.category FROM gamedata gd WHERE gd.category = $category ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}


// Function to fetch filtered data from the database based on the search term
function filterGameData($searchTerm, $currentCategory) {
    global $conn;

    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
    $sql = "SELECT name FROM gamedata WHERE LOWER(name) LIKE '%$searchTerm%' AND category = $currentCategory";
    $result = $conn->query($sql);

    $filteredData = [];
    while ($row = $result->fetch_assoc()) {
        $filteredData[] = $row;
    }

    return $filteredData;
}

// Check if the request is an AJAX request to filter game data
if (isset($_GET['searchTerm']) && isset($_GET['currentCategory'])) {
    $searchTerm = $_GET['searchTerm'];
    $currentCategory = $_GET['currentCategory']; // Pass the current category
    $filteredData = filterGameData($searchTerm, $currentCategory);

    header('Content-Type: application/json');
    echo json_encode($filteredData);
    exit; // Terminate script after sending JSON response
}


// Update user experience and level in the database
function updateUserExperience($conn, $username, $expChange) {
    // Ensure experience doesn't go negative
    $sql = "UPDATE user SET exp = GREATEST(0, exp + $expChange) WHERE username = '$username'";
    $conn->query($sql);

    // Get updated user data
    $user = $conn->query("SELECT exp, lvl FROM user WHERE username = '$username'")->fetch_assoc();

    // Get the total_exp required for the current level
    $levelData = $conn->query("SELECT total_exp FROM levels WHERE id = {$user['lvl']}")->fetch_assoc();

    // Check if levelData is not null before accessing it
    if ($levelData !== null && $user['exp'] >= $levelData['total_exp']) {
        $nextLevel = $user['lvl'] + 1;

        // Check if there's a row for the next level in the levels table
        $nextLevelData = $conn->query("SELECT id FROM levels WHERE id = $nextLevel")->fetch_assoc();
        if ($nextLevelData !== null) {  // Proceed with level up only if the next level exists
            // Corrected line:
            $conn->query("UPDATE user SET lvl = $nextLevel, exp = 0 WHERE username = '$username'");
        }
    }
}
// Get user information based on username from session
$username = $_SESSION["username"];
$sql = "SELECT u.*, l.name AS level_name, l.total_exp AS next_level_exp 
        FROM user u 
        JOIN levels l ON u.lvl = l.id 
        WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);  // Assuming you have a $conn variable for your database connection
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle case where user is not found (e.g., redirect or display error)
}

mysqli_stmt_close($stmt);

// Initialize variables
$username = $_SESSION['username'];
$message = ""; // Initialize message variable outside the if statement

// Get or set the chosen category 
if (isset($_POST['category'])) {
    $chosenCategory = $_POST['category'];
    $_SESSION['chosenCategory'] = $chosenCategory; // Store in session
    $message = ""; // Clear message if the category changes.
} elseif (isset($_SESSION['chosenCategory'])) {
    $chosenCategory = $_SESSION['chosenCategory']; // Retrieve from session
} else {
    $chosenCategory = 1; // Default to Agents
}

// Load a challenge 
if (!isset($_SESSION['current_challenge']) || isset($_POST['category'])) { // Load new challenge if a guess was made, if the category changed, or if there is no challenge in the session
    $currentChallenge = getChallenge($chosenCategory);
    $_SESSION['current_challenge'] = $currentChallenge;
} else {
    $currentChallenge = $_SESSION['current_challenge'];
}

// Handle form submission 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['guess'])) {
        // User submitted a guess
        $guess = strtolower($_POST['guess']);
        $correctAnswer = strtolower($_SESSION['current_challenge']['name']);

        if ($guess == $correctAnswer) {
            $message = "<p class='correct'>Correct! +5 Experience.</p>";
            updateUserExperience($conn, $username, 5);
        } else {
            $message = "<p class='incorrect'>Incorrect guess. -1 Experience.</p>";
            updateUserExperience($conn, $username, -1);
        }

        // Load a new challenge after each guess
        $currentChallenge = getChallenge($chosenCategory);
        $_SESSION['current_challenge'] = $currentChallenge;
        // Re-fetch user data after updating experience
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result); // Update $user with new values
        } else {
            // Handle case where user is not found
        }

        mysqli_stmt_close($stmt);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Fanpage - Freeplay Mode</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .correct {
            color: green;
        }

        .incorrect {
            color: red;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: white;
        }

        .game-container {
            background-color: #282828;
            padding: 2rem;
            border-radius: 0.625rem;
            text-align: center;
            width: 100%;
            max-width: 600px;
            margin: 2rem auto;
        }

        .challenge-image img {
            max-width: 100%;
            height: auto;
            margin-bottom: 1.25rem;
        }

        #guess-input {
            padding: 0.625rem;
            margin-right: 0.625rem;
            border: 1px solid #555;
            border-radius: 0.3125rem;
            background-color: #383838;
            color: white;
        }

        #search-results {
            margin-top: 0.625rem;
            text-align: left;
            max-height: 12.5rem;
            overflow-y: auto;
        }

        .search-result {
            padding: 0.5rem;
            border-bottom: 1px solid #555;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-result:hover {
            background-color: #484848;
        }

        .exp-bar {
            background-color: #333;
            padding: 0.625rem;
            min-height: 2.5rem;
            text-align: center;
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .exp-bar div {
            background-color: #007bff;
            height: 0.625rem;
            margin-bottom: 0.3125rem;
        }

        .exp-bar span {
            color: white;
            display: block;
        }

        #level-up-message {
            margin-top: 0.625rem;
            text-align: center;
            color: #ff4500;
        }

        .category-form select {
            background-color: #383838;
            color: white;
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: 0.3125rem;
            cursor: pointer;
        }

        button[type="submit"] {
            padding: 0.625rem 1.25rem;
            background-color: #ff4500;
            color: white;
            border: none;
            border-radius: 0.3125rem;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="game-container mt-sm-5">
            <?php if (isset($message)) echo $message; ?>
            <form method="post" action="">
                <div class="category-form mb-3">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="1" <?php if ($chosenCategory == 1) echo 'selected'; ?>>Agents</option>
                        <option value="2" <?php if ($chosenCategory == 2) echo 'selected'; ?>>Weapons</option>
                        <option value="3" <?php if ($chosenCategory == 3) echo 'selected'; ?>>Sprays</option>
                        <option value="4" <?php if ($chosenCategory == 4) echo 'selected'; ?>>Player Cards</option>
                        <option value="5" <?php if ($chosenCategory == 5) echo 'selected'; ?>>Buddies</option>
                        <option value="6" <?php if ($chosenCategory == 6) echo 'selected'; ?>>Skins</option>
                    </select>
                </div>
            </form>
            <form method="post" action="">
                <div class="challenge-image">
                    <img src="<?php echo $currentChallenge['img']; ?>" alt="Challenge Image">
                </div>

                <input type="hidden" name="currentCategory" value="<?php echo $chosenCategory; ?>">
                <div class="input-group mb-3">
                    <input type="text" name="guess" id="guess-input" class="form-control" placeholder="Type your guess...">
                </div>
                <button type="submit" class="btn btn-danger">Submit Guess</button>
                <div id="search-results" class="list-group"></div>
            </form>
        </div>
    </div>

    <div class="exp-bar">
        <span>Level: <?php echo $user['level_name']; ?> EXP: <?php echo $user['exp']; ?> / <?php echo $user['next_level_exp']; ?></span>
        <div style="width: <?php echo ($user['exp'] / $user['next_level_exp']) * 100; ?>%;"></div>
    </div>

    <script>
        const guessInput = document.getElementById('guess-input');
        const searchResults = document.getElementById('search-results');

        guessInput.addEventListener('input', () => {
            const searchTerm = guessInput.value.toLowerCase();
            const currentCategory = document.querySelector('input[name="currentCategory"]').value;

            if (searchTerm.length > 0) {
                fetch(`freeplay.php?searchTerm=${searchTerm}&currentCategory=${currentCategory}`)
                    .then(response => response.json())
                    .then(filteredData => {
                        searchResults.innerHTML = '';

                        filteredData.forEach(item => {
                            const resultDiv = document.createElement('div');
                            resultDiv.classList.add('list-group-item', 'search-result');
                            resultDiv.textContent = item.name;
                            resultDiv.dataset.name = item.name;

                            resultDiv.addEventListener('click', () => {
                                guessInput.value = item.name;
                                searchResults.innerHTML = '';
                            });

                            searchResults.appendChild(resultDiv);
                        });
                    });
            } else {
                searchResults.innerHTML = '';
            }
        });
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
