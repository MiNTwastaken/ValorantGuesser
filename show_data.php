<?php
// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION["username"]) || $_SESSION["username"] != "admin") {
  header("Location: login.php"); // Redirect to login if not admin
    exit;
}

// Get selected data type from form submission
$dataType = isset($_POST["data_type"]) ? $_POST["data_type"] : null;
if (empty($dataType) && isset($_GET["data_type"])) {
    $dataType = $_GET["data_type"];
}

// Connect to database
$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

// Check connection
if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}

// Function to display data based on type
function showData($dataType, $connection) {
  $sql = "SELECT * FROM " . $dataType;
  $result = mysqli_query($connection, $sql);

  if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr>";
    // Add table headers
    if ($dataType == "quote") {
      echo "<th>Quote</th>";
      echo "<th>Agent Name</th>";
    } else {
      echo "<th>Name</th>";
      echo "<th>Image</th>";
    }
    echo "<th>Actions</th>";
    echo "</tr>";

    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      // Display data based on type
      if ($dataType == "quote") {
        echo "<td>" . $row["quote"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
      } else {
        echo "<td>" . $row["name"] . "</td>";
        echo "<td><img src='" . $row["img"] . "' alt='" . $row["name"] . "'></td>";
      }
      echo "<td><a href='edit_data.php?id=" . $row["id"] . "&data_type=" . $dataType . "'>Edit</a> | <a href='delete_data.php?id=" . $row["id"] . "&data_type=" . $dataType . "'>Delete</a></td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    echo "No data found for " . $dataType;
  }
}

// Call the showData function based on selected data type
showData($dataType, $connection);

// Close connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Valorant Guesser Admin Panel</title>
  <link rel="stylesheet" href="styles.css">
    <style>
    img {
        /* Set the desired width and height */
        width: 100px;
        height: 100px;
        /* Optional: Maintain aspect ratio if needed */
        object-fit: cover;
    }
    </style>
</head>
<body>
</body>
</html>