<!DOCTYPE html>
<html>
	<head>
		<title>Congratulations!</title>
		<link rel="stylesheet" href="styles.css">
	</head>
	<body>
		<h1>Congratulations!</h1>
		<p>You are a genius!</p>
		<form method="post">
			<label for="name">Enter your name:</label>
			<input type="text" name="name" id="name">
			<input type="submit" name="submit" value="Save">
		</form>
		<a href="index.php" class="button">Back to Menu</a>

		<?php
			$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
			if (!$connection) {
				die("Connection failed: " . mysqli_connect_error());
			}

			if (isset($_POST["submit"])) {
				$name = trim($_POST["name"]);
				$name = stripslashes($name);
				$name = htmlspecialchars($name);
				if (empty($name)) {
					echo "<div class='error'>Please enter a name.</div>";
				} else {
					$query = "INSERT INTO genius (name) VALUES ('$name')";
					if (mysqli_query($connection, $query)) {
						header("Location: genius.php");
						exit();
					} else {
						echo "<div class='error'>Error: " . mysqli_error($connection) . "</div>";
					}
				}
			}
			mysqli_close($connection);
		?>
	</body>
</html>
