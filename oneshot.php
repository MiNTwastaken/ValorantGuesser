<!DOCTYPE html>
<html>
<head>
    <title>Valorant Guesser</title>
    <link rel="stylesheet" href="styles.css">

    <script>
    // Function to hide the placeholder option after page load
    window.onload = function() {
        const placeholderOption = document.querySelector('.placeholder-option');
        placeholderOption.style.display = 'none';
    }
    </script>
</head>
<body>
    <?php
    session_start();

    // Set up database connection
    $connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

    // Check connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (!isset($_SESSION["tries"]) || !isset($_SESSION["table_index"]) || !isset($_SESSION["name"]) || !isset($_SESSION["quote"]) || !isset($_SESSION["image_path"])) {
        $_SESSION["tries"] = 1;
        $_SESSION["table_index"] = 0;
        $_SESSION["name"] = "";
        $_SESSION["quote"] = "";
        $_SESSION["image_path"] = "";
    }

    // If form is submitted, process the guess
    if (isset($_POST["submit"])) {
        $guess = $_POST["guess"];
        if ($guess === $_SESSION["name"]) {
            $_SESSION["tries"] = 1;
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
            $_SESSION["tries"] = 1;
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
        $tables = array("ability", "agent", "graffiti", "playercard", "weapon", "quote");
        $table = $tables[$_SESSION["table_index"]];

        if ($table === "quote") {
        $query = "SELECT quote, `name` FROM quote ORDER BY RAND() LIMIT 1";
        } else {
        $query = "SELECT `name`, img FROM $table ORDER BY RAND() LIMIT 1";
        }

        if (isset($query)) {
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
        }

        // Output the image or quote and the guess form
        if ($table === "quote") {
            echo "<blockquote class='centered'>{$_SESSION['quote']}</blockquote><br>";
          } else {
            echo "<img class='img-size' src='{$_SESSION['image_path']}'><br>";
          }

            // Get the current answer from the session
            $current_answer = $_SESSION["name"];

            // Get all possible answers from the current table based on the session variable
            $tables = array("ability", "agent", "graffiti", "playercard", "weapon", "quote");
            $table = $tables[$_SESSION["table_index"]];

            $query = "SELECT `name` FROM $table";
            $result = mysqli_query($connection, $query);
            $answers = [];
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $answers[] = $row["name"];
            }
            }
            echo "<form method='post'>";
            echo "<label for='guess'>Guess:</label>";
            echo "<select name='guess' id='guess'>";

            // Add an empty option with a placeholder class for styling
            echo "<option value='' class='placeholder-option'>Select an answer</option>";

            foreach ($answers as $answer) {
            echo "<option value='$answer'>$answer</option>";
            }

            echo "</select>";
            echo "<input type='submit' name='submit' value='Submit'>";
            echo "</form>";
            echo "Tries left: {$_SESSION['tries']}";
    }

    // Close database connection
    mysqli_close($connection);
    ?>
</body>
</html>
