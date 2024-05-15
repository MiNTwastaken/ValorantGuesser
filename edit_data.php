<?php
// Start session
session_start();

if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== 1) {
  header("Location: login.php");
  exit;
}

// Get data type and ID from URL parameters
$dataType = $_GET["data_type"];
$id = $_GET["id"];

// Connect to database
$connection = mysqli_connect("localhost:3306", "root", "", "valorantguesser");

// Check connection
if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}

// Function to get data by ID
function getDataById($dataType, $id, $connection) {
  $sql = "SELECT * FROM " . $dataType . " WHERE id = " . $id;
  $result = mysqli_query($connection, $sql);

  if (mysqli_num_rows($result) > 0) {
    return mysqli_fetch_assoc($result);
  } else {
    return false;
  }
}

// Get data for the specific ID
$data = getDataById($dataType, $id, $connection);

// Check if data found
if (!$data) {
  echo "Data not found";
  exit;
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Update data based on data type
  $updateSuccess = updateData($dataType, $id, $connection, $data);

  // Close connection
  mysqli_close($connection);

  if ($updateSuccess) {
    header("Location: show_data.php?data_type=" . $dataType . "&success=1"); // Redirect with success message
    exit;
  } else {
    // Handle update failure
    echo "Failed to update data.";
  }
}

// Close connection after retrieving data
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Valorant Fanpage Admin Panel - Edit <?php echo ucfirst($dataType); ?></title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Edit <?php echo ucfirst($dataType); ?></h1>

  <form method="post" action="update_data.php">
    <input type="hidden" name="data_type" value="<?php echo $dataType; ?>"> <input type="hidden" name="id" value="<?php echo $id; ?>"> <?php
    if ($dataType == "quote") {
      echo "<label for='quote'>Quote:</label>";
      echo "<textarea name='quote' id='quote' required>" . $data["quote"] . "</textarea>";
      echo "<label for='agent_name'>Agent Name:</label>";
      echo "<input type='text' name='agent_name' id='agent_name' value='" . $data["name"] . "' required>";
    } else {
      echo "<label for='name'>Name:</label>";
      echo "<input type='text' name='name' id='name' value='" . $data["name"] . "' required>";
      echo "<label for='img'>Image:</label>";
      echo "<input type='text' name='img' id='img' value='" . $data["img"] . "' required>";
    }
    ?>
    <button type="submit">Update</button>
  </form>

  <a href="show_data.php?data_type=<?php echo $dataType; ?>">Back to List</a>

</body>
</html>
