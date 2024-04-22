<!DOCTYPE html>
<html>
<head>
    <title>Valorant Guesser</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
    // Set up database connection
    $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

    // Check connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Function to initialize a new user
    function initializeUser() {
        global $connection;

        // Retrieve the last user ID from the database
        $queryLastId = "SELECT MAX(id) AS last_id FROM user";
        $resultLastId = mysqli_query($connection, $queryLastId);

        if ($resultLastId) {
            $rowLastId = mysqli_fetch_assoc($resultLastId);
            $lastId = $rowLastId['last_id'];
            $userId = $lastId + 1;
        } else {
            // If there are no users in the database, start with user ID 1
            $userId = 1;
        }

        setcookie("user_id", $userId, time() + (86400 * 30), "/"); // Cookie lasts for 30 days

        // Insert a new user into the database
        $queryInsertUser = "INSERT INTO user (id, tries, current_table, winner) VALUES ('$userId', 5, 0, 0)";
        mysqli_query($connection, $queryInsertUser);
    }

    // Function to process the user's guess
    function processGuess($guess) {
        global $connection;

        // Retrieve user's current progress from the database
        $userId = $_COOKIE["user_id"];
        $querySelectUser = "SELECT * FROM user WHERE id = ?";
        $stmtSelectUser = mysqli_prepare($connection, $querySelectUser);
        mysqli_stmt_bind_param($stmtSelectUser, "i", $userId);
        mysqli_stmt_execute($stmtSelectUser);
        $resultSelectUser = mysqli_stmt_get_result($stmtSelectUser);

        if ($resultSelectUser) {
            $rowUser = mysqli_fetch_assoc($resultSelectUser);

            // Get the current table based on the user's progress
            $tables = array("ability", "agent", "graffiti", "playercard", "weapon", "quote");
            $currentTable = $tables[$rowUser["current_table"]];

            // Check if the user's guess is correct
            $querySelectChallenge = "SELECT dailychallenge.id, $currentTable.name, $currentTable.img, quote.quote
                FROM dailychallenge
                JOIN $currentTable ON dailychallenge.{$currentTable}_id = $currentTable.id
                LEFT JOIN quote ON dailychallenge.quote_id = quote.id
                WHERE dailychallenge.id = 1"; // Change the ID based on your requirements

            $stmtSelectChallenge = mysqli_prepare($connection, $querySelectChallenge);

            if ($stmtSelectChallenge) {
                mysqli_stmt_execute($stmtSelectChallenge);
                $resultSelectChallenge = mysqli_stmt_get_result($stmtSelectChallenge);

                if ($resultSelectChallenge) {
                    $rowChallenge = mysqli_fetch_assoc($resultSelectChallenge);

                    if ($guess == $rowChallenge["name"]) {
                        // Reset tries and update current table
                        $queryUpdateUser = "UPDATE user SET tries = 5, current_table = current_table + 1 WHERE id = ?";
                        $stmtUpdateUser = mysqli_prepare($connection, $queryUpdateUser);
                        mysqli_stmt_bind_param($stmtUpdateUser, "i", $userId);
                        mysqli_stmt_execute($stmtUpdateUser);

                        // Check if the user has answered all questions
                        if ($rowUser["current_table"] >= 6) {
                            // Display "You've won" if all questions are answered
                            echo "You've won!";
                            echo "<form method='post'><input type='submit' name='retry' value='Retry'>";
                            echo "<a href='index.php'><button type='button'>Main Screen</button></a></form>";
                            exit; // Stop execution to prevent further output
                        }
                    } else {
                        // Decrement tries
                        $queryUpdateUser = "UPDATE user SET tries = tries - 1 WHERE id = ?";
                        $stmtUpdateUser = mysqli_prepare($connection, $queryUpdateUser);
                        mysqli_stmt_bind_param($stmtUpdateUser, "i", $userId);
                        mysqli_stmt_execute($stmtUpdateUser);
                    }

                    // Redirect to avoid form resubmission
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit;
                } else {
                    echo "Error in fetching challenge data: " . mysqli_error($connection);
                }
            } else {
                echo "Error in preparing the statement: " . mysqli_error($connection);
            }
        }
    }

    // Function to display the challenge
    function displayChallenge() {
        global $connection;

        // Retrieve user's current progress from the database
        $userId = $_COOKIE["user_id"];
        $querySelectUser = "SELECT * FROM user WHERE id = ?";
        $stmtSelectUser = mysqli_prepare($connection, $querySelectUser);
        mysqli_stmt_bind_param($stmtSelectUser, "i", $userId);
        mysqli_stmt_execute($stmtSelectUser);
        $resultSelectUser = mysqli_stmt_get_result($stmtSelectUser);

        if ($resultSelectUser) {
            $rowUser = mysqli_fetch_assoc($resultSelectUser);

            // Get the current table based on the user's progress
            $tables = array("ability", "agent", "graffiti", "playercard", "weapon", "quote");
            $currentTable = $tables[$rowUser["current_table"]];

            // Select the specific challenge from dailychallenge with ID 1
            $querySelectChallenge = "SELECT dailychallenge.id, {$currentTable}.name, {$currentTable}.img, quote.quote
                FROM dailychallenge
                JOIN {$currentTable} ON dailychallenge.{$currentTable}_id = {$currentTable}.id
                LEFT JOIN quote ON dailychallenge.quote_id = quote.id
                WHERE dailychallenge.id = 1";
            $stmtSelectChallenge = mysqli_prepare($connection, $querySelectChallenge);

            if ($stmtSelectChallenge) {
                mysqli_stmt_execute($stmtSelectChallenge);
                $resultSelectChallenge = mysqli_stmt_get_result($stmtSelectChallenge);

                if ($resultSelectChallenge) {
                    $rowChallenge = mysqli_fetch_assoc($resultSelectChallenge);

                    // Output the image or quote and the guess form
                    if (isset($rowChallenge["img"])) {
                        echo "<img class='img-size' src='{$rowChallenge['img']}'><br>";
                    } elseif (isset($rowChallenge["quote"])) {
                        echo "<blockquote class='centered'>{$rowChallenge['quote']}</blockquote><br>";
                    }

                    // Output the guess form
                    echo "<form method='post'>";
                    echo "<label for='guess'>Guess:</label>";
                    echo "<input type='text' name='guess' id='guess'>";
                    echo "<input type='submit' name='submit' value='Submit'>";
                    echo "</form>";

                    // Output the try count
                    echo "Tries left: {$rowUser['tries']}";
                } else {
                    echo "Error in fetching challenge data: " . mysqli_error($connection);
                }
            } else {
                echo "Error in preparing the statement: " . mysqli_error($connection);
            }
        }
    }

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST["submit"])) {
            processGuess($_POST["guess"]);
        }
    }

    // Check if user is initialized
    if (!isset($_COOKIE["user_id"])) {
        initializeUser();
    }

    // Display challenge
    displayChallenge();

    // Close database connection
    mysqli_close($connection);
    ?>
</body>
</html>
