<!DOCTYPE html>
<html>
	<head>
		<title>Valorant Guesser</title>
		<link rel="stylesheet" href="styles.css">
	</head>
	<body>
		<?php
		session_start();

		// Set up database connection (replace host, username, password, and database with your own values)
		$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

		// Check connection
		if (!$connection) {
			die("Connection failed: " . mysqli_connect_error());
		}

		if (!isset($_SESSION["tries"]) || !isset($_SESSION["table_index"]) || !isset($_SESSION["answer"])) {
			$_SESSION["tries"] = 5;
			$_SESSION["table_index"] = 0;
			$_SESSION["answer"] = "";
		}
		
		// If form is submitted, process the guess
		if (isset($_POST["submit"])) {
			$guess = $_POST["guess"];
			if ($guess === $_SESSION["answer"]) {
				$_SESSION["tries"] = 5;
				$_SESSION["answer"] = "";
				$_SESSION["table_index"]++;
				// Reset table index to 0 if it exceeds the number of tables
				if ($_SESSION["table_index"] >= 6) {
					header("Location: input.php");
	                exit();
				}
			} else {
				$_SESSION["tries"]--;
			}
		}
		else {
			// Only set initial game state if the form has not been submitted
			if (!isset($_SESSION["tries"])) {
				$_SESSION["tries"] = 5;
			}
			if (!isset($_SESSION["table_index"])) {
				$_SESSION["table_index"] = 0;
			}
			if (!isset($_SESSION["answer"])) {
				$_SESSION["answer"] = "";
			}
		}
		
		// If there are no more tables or no more tries left, end the game
		if ($_SESSION["table_index"] >= 6 || $_SESSION["tries"] <= 0) {
			echo "Game over";
			echo "<form method='post'><input type='submit' name='try_again' value='Try again'></form>";
			session_destroy();
		} else {
			// Select a random image path or quote and answer from the current table
			$tables = array("ability", "agent", "graffiti", "playercard", "weapon", "quote");
			$table = $tables[$_SESSION["table_index"]];
			if ($table === "quote") {
				$query = "SELECT quote, answer FROM quote WHERE id = 1";
			} else {
				$query = "SELECT answer, img FROM $table WHERE id = 1";
			}
			$result = mysqli_query($connection, $query);
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				$_SESSION["answer"] = $row["answer"];
				if ($table === "quote") {
					$quote = $row["quote"];
					$image_path = "";
				} else {
					$image_path = $row["img"];
					$quote = "";
				}
			}

			// Output the image or quote and the guess form
			if ($table === "quote") {
				echo "<blockquote class='centered'>$quote</blockquote><br>";
			} else {
				echo "<img class='img-size' src='$image_path'><br>";
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
	</body>
</html>
