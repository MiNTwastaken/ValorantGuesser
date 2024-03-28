<?php
// Start session (if not already started)
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION["username"]) || $_SESSION["username"] != "admin") {
  header("Location: login.php"); // Redirect to login if not admin
    exit;
}

// Get data from form submission
$dataType = $_POST["data_type"];
$id = $_POST["id"];
// Modify placeholders based on data type
if ($dataType == "quote") {
  $quote = htmlspecialchars($_POST["quote"]);
  $agentName = htmlspecialchars($_POST["name"]);
} else {
  $name = htmlspecialchars($_POST["name"]);
  $img = htmlspecialchars($_POST["img"]);
}

// Connect to database
$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

// Check connection
if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}

// Function to update data
function updateData($dataType, $id, $connection, $data) {
    $sql = "UPDATE " . $dataType . " SET ";
    $isFirst = true;
    // Construct the SET clause based on data type
    if ($dataType == "quote") {
      $sql .= "quote = '" . $data["quote"] . "', ";
      $sql .= "name = '" . $data["name"] . "'";
    } else {
      $sql .= "name = '" . $data["name"] . "', ";
      $sql .= "img = '" . $data["img"] . "'";
    }
    $sql .= " WHERE id = " . $id;
  
    if (mysqli_query($connection, $sql)) {
      return true;
    } else {
      echo "Error updating data: " . mysqli_error($connection);
      return false;
    }
  }
  
  // Update data based on data type
  $updateSuccess = updateData($dataType, $id, $connection, $data); // Pass data as associative array
  
  // Close connection
  mysqli_close($connection);
  
  if ($updateSuccess) {
    header("Location: show_data.php?data_type=" . $dataType . "&success=1"); // Redirect with success message
    exit;
  } else {
    // Handle update failure
    echo "Failed to update data.";
  }
?>