<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION["username"]) || (isset($_SESSION["admin"]) && $_SESSION["admin"] != 1)) {
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

// Function to fetch and process daily challenges
function getDailyChallenges() {
    global $conn;
    $challenges = [];

    // Fetch today's challenges from dailychallenge
    $challengesQuery = "SELECT dc.id AS challenge_id, gd.category, gd.name, gd.img 
    FROM dailychallenge dc 
    JOIN gamedata gd ON dc.gamedata_id = gd.id";
    $challengesResult = mysqli_query($conn, $challengesQuery);

    while ($row = mysqli_fetch_assoc($challengesResult)) {
        $challenges[] = $row;
    }
    return $challenges;
}


// Get the challenges
$challenges = getDailyChallenges();
$currentChallengeIndex = isset($_SESSION['current_challenge']) ? $_SESSION['current_challenge'] : 0;



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


?>

<!DOCTYPE html>
<html>
<head>
    <title>Valorant Fanpage - Daily Challenge</title>
    <link rel="stylesheet" href="styless.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: white;
        }

        .game-container {
            background-color: #282828;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .challenge-image img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        #guess-input {
            width: calc(100% - 100px);
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #383838;
            color: white;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #ff4500;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #search-results {
            margin-top: 10px;
            text-align: left;
            max-height: 200px; /* Set a maximum height for the results container */
            overflow-y: auto; /* Add a vertical scrollbar if results exceed the height */
        }

        .search-result {
            padding: 8px;
            border-bottom: 1px solid #555;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-result:hover {
            background-color: #484848;
        }
        .navbar {
            background-color: red;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
        }

    </style>
</head>
<body>
    <div class="game-container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check if a guess was submitted through the form
            if (isset($_POST['guess'])) {
                $guess = strtolower($_POST['guess']);
                $correctAnswer = strtolower($challenges[$currentChallengeIndex]['name']);
        
                if ($guess == $correctAnswer) {
                    // Correct guess
                    $_SESSION['current_challenge']++;
                    $currentChallengeIndex++;
                    if ($currentChallengeIndex >= count($challenges)) {
                        echo "<p>You won! All challenges completed.</p>";
                        unset($_SESSION['current_challenge']); // Reset for the next game
                    } else {
                        echo "<p>Correct! Next challenge...</p>";
                    }
                } else {
                    // Incorrect guess
                    echo "<p>Incorrect guess. Try again.</p>";
                }
            }
        }
        ?>
        <?php if (!empty($challenges) && $currentChallengeIndex < count($challenges)): 
            $currentChallenge = $challenges[$currentChallengeIndex];
        ?>
        <form method="post" action="">
        <div class="challenge-image">
            <img src="<?php echo $currentChallenge['img']; ?>" alt="Challenge Image">
        </div>
        <div class="input-container">
            <input type="text" name="guess" id="guess-input" placeholder="Type your guess...">
            <button type="submit">Submit Guess</button>
        </div>

        <div id="search-results"></div>
            <input type="hidden" name="currentCategory" value="
        <?php
            echo $currentChallenge['category']; 
            ?>">
        </form>
        <?php elseif (empty($challenges)): ?>
            <p>No daily challenges available yet.</p>
        <?php else: ?>
            <form method="post" action="">  
                <button type="submit" name="replay">Replay</button> 
            </form>
        <?php endif; ?>
    </div>
    <?php
    // Handle the replay button click
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['replay'])) {
        unset($_SESSION['current_challenge']); // Reset the current challenge index
        header("Location: dailychallenge.php"); // Reload the page to start over
        exit();
    }
    ?>
    <script>
        const guessInput = document.getElementById('guess-input');
        const searchResults = document.getElementById('search-results');
        let selectedResult = null;

        guessInput.addEventListener('input', () => {
            const searchTerm = guessInput.value.toLowerCase();
            const currentCategory = document.querySelector('input[name="currentCategory"]').value; 

            // Clear previous results immediately
            searchResults.innerHTML = ''; 

            if (searchTerm.length > 0) { // Only fetch if there's something to search
                fetch(`dailychallenge.php?searchTerm=${searchTerm}&currentCategory=${currentCategory}`)
                    .then(response => response.json())
                    .then(filteredData => {
                        filteredData.forEach(item => {
                            const resultDiv = document.createElement('div');
                            resultDiv.classList.add('search-result');
                            resultDiv.textContent = item.name;
                            resultDiv.dataset.name = item.name;

                            resultDiv.addEventListener('click', () => {
                                guessInput.value = item.name;
                                selectedResult = item.name;
                                searchResults.innerHTML = ''; 
                                document.querySelector('form').submit(); 
                            });

                            searchResults.appendChild(resultDiv);
                        });
                    });
            }
        });

    </script>
    <div class="navbar">
        <div class="container">
            <a href="index.php">Valorant Fanpage</a>
            <nav>
                <div class="dropdown">
                    <a href="wiki.php" class="dropdown-btn">Wiki</a>
                    <div class="dropdown-content">
                        <a href="wiki.php#agents">Agents</a>
                        <a href="wiki.php#weapons">Weapons</a>
                        <a href="wiki.php#maps">Maps</a>
                        <a href="wiki.php#skins">Skins</a>
                    </div>
                </div>

                <div class="dropdown">
                    <a href="social.php" class="dropdown-btn">Social</a>
                    <div class="dropdown-content">
                        <a href="social.php#general">General Discussion</a>
                        <a href="social.php#competitive">Competitive Play</a>
                        <a href="social.php#lore">Lore & Story</a>
                        <a href="social.php#creations">Community Creations</a>
                    </div>
                </div>
                
                <div class="dropdown">
                    <a href="minigames.php" class="dropdown-btn">Minigames</a>
                    <div class="dropdown-content">
                        <a href="dailychallenge.php">Daily Quiz</a>
                        <a href="aimtrainer.php">One Shot</a>
                        <a href="freeplay.php">Free Play</a>
                        <a href="leaderboard.php">Leaderboard</a>
                    </div>
                </div>

                <?php
                $isLoggedIn = isset($_SESSION["username"]);
                ?>

                <?php if ($isLoggedIn && isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) : ?>
                    <div class="dropdown">
                        <a href="admin.php" class="dropdown-btn">Admin Panel</a>
                        <div class="dropdown-content">
                            <a href="admin.php">Manage Users</a>
                            <a href="gamedata.php">Manage Game Data</a>
                            <a href="posts.php">Manage Posts</a>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if ($isLoggedIn) : ?>
                    <div class="logged-in-user">
                        <a href="profile.php" class="profile-link"><?php echo $_SESSION["username"]; ?></a>
                        <form action="logout.php" method="post">
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                <?php else : ?>
                    <a href="login.php" class="login-btn">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</body>
</html>