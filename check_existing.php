<?php
$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$field = isset($_POST['username']) ? 'username' : (isset($_POST['email']) ? 'email' : '');
$value = mysqli_real_escape_string($connection, $field === 'username' ? $_POST['username'] : $_POST['email']);

if ($field === 'username' || $field === 'email') {
    $sql = "SELECT 1 FROM user WHERE $field = '$value'";
    $result = mysqli_query($connection, $sql);
    echo json_encode(['exists' => mysqli_num_rows($result) > 0]);
} else {
    echo json_encode(['exists' => false]);
}

mysqli_close($connection);
?>
