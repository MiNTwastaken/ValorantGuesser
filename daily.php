<?php
session_start();

// Generate a unique identifier for the user and store it in a cookie
if (!isset($_COOKIE["user_identifier"])) {
    $userIdentifier = uniqid("user_", true);
    setcookie("user_identifier", $userIdentifier, time() + (86400 * 30), "/"); // Cookie lasts for 30 days
}

// Set up database connection
$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION["tries"]) || !isset($_SESSION["table_index"]) || !isset($_SESSION["name"]) || !isset($_SESSION["quote"]) || !isset($_SESSION["image_path"])) {
    $_SESSION["tries"] = 5;
    $_SESSION["table_index"] = 0;
    $_SESSION["name"] = "";
    $_SESSION["quote"] = "";
    $_SESSION["image_path"] = "";
}

// If form is submitted, process the guess
if (isset($_POST["submit"])) {
    $guess = $_POST["guess"];
    if ($guess === $_SESSION["name"]) {
        $_SESSION["tries"] = 5;
        $_SESSION["name"] = "";
        $_SESSION["quote"] = "";
        $_SESSION["image_path"] = "";
        $_SESSION["table_index"]++;
        // Reset table index to 0 if it exceeds the number of tables
        if ($_SESSION["table_index"] >= 6) {
            // Display "You've won"
            echo "You've won!";
            echo "<form method='post'><input type='submit' name='retry' value='Retry'>";
            echo "<a href='index.php'><button type='button'>Main Screen</button></a></form>";
            session_destroy();
            exit; // Stop execution to prevent further output
        }
    } else {
        $_SESSION["tries"]--;
    }
} else {
    // Only set initial game state if the form has not been submitted
    if (!isset($_SESSION["tries"])) {
        $_SESSION["tries"] = 5;
    }
    if (!isset($_SESSION["table_index"])) {
        $_SESSION["table_index"] = 0;
    }
    if (!isset($_SESSION["name"])) {
        $_SESSION["name"] = "";
    }
    if (!isset($_SESSION["quote"])) {
        $_SESSION["quote"] = "";
    }
    if (!isset($_SESSION["image_path"])) {
        $_SESSION["image_path"] = "";
    }
}

// If there are no more tables or no more tries left, end the game
if ($_SESSION["table_index"] >= 6 || $_SESSION["tries"] <= 0) {
    echo "Game over";
    echo "<form method='post'><input type='submit' name='try_again' value='Try again'>";
    echo "<a href='index.php'><button type='button'>Main Screen</button></a></form>";
    session_destroy();
} else {
    // Select a random image path or quote and answer from the current table
    $tables = array("ability", "agent", "graffiti", "playercard", "weapon", "quote");
    $table = $tables[$_SESSION["table_index"]];

    // Retrieve preselected question from the dailychallenge table based on user preferences
    $userIdentifier = $_COOKIE["user_identifier"];

    $query = "SELECT $table.`name`, $table.img, quote.quote
              FROM dailychallenge
              JOIN $table ON dailychallenge.${table}_id = $table.${table}_id
              LEFT JOIN quote ON dailychallenge.quote_id = quote.quote_id
              WHERE dailychallenge.user_identifier = '$userIdentifier'
              ORDER BY RAND() LIMIT 1";

    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION["name"] = $row["name"];
        if ($table === "quote") {
            $_SESSION["quote"] = $row["quote"];
        } else {
            $_SESSION["image_path"] = $row["img"];
        }
    }

    // Output the image or quote and the guess form
    if ($table === "quote") {
        echo "<blockquote class='centered'>{$_SESSION['quote']}</blockquote><br>";
    } else {
        echo "<img class='img-size' src='{$_SESSION['image_path']}'><br>";
    }
    echo "<form method='post'>";
    echo "<label for='guess'>Guess:</label>";
    echo "<input type='text' name='guess' id='guess'>";
    echo "<input type='submit' name='submit' value='Submit'>";
    echo "</form>";
    echo "Tries left: {$_SESSION['tries']}";
}

// Close database connection
mysqli_close($connection);
?>
