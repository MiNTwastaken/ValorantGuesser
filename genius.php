<!DOCTYPE html>
<html>
	<head>
		<title>Geniuses</title>
		<link rel="stylesheet" href="styles.css">
	</head>
	<body>
	<h1>List of today's Geniuses</h1>
	<div class="genius-container">
		<?php
			$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");
			if (!$connection) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$query = "SELECT name FROM genius";
			$result = mysqli_query($connection, $query);
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {
					echo "<div class='genius'>" . $row["name"] . "</div>";
				}
			} else {
				echo "No geniuses found.";
			}
			mysqli_close($connection);
		?>
	</div>
    <a href="index.php" class="button">Back to Menu</a>
    </body>
</html>
