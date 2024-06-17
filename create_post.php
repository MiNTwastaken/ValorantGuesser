<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$connection = mysqli_connect("localhost:3306", "root", "", "valorantfanpage");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars(trim($_POST["title"]));
    $content = htmlspecialchars(trim($_POST["content"]));
    $createdBy = $_SESSION["username"];
    $tags_id = (int)$_POST["tags_id"];

    // Input Validation
    if (empty($title) || empty($content)) {
        echo "Title and content are required fields.";
        exit;
    }

    // Handle Media Uploads
    $uploadedMedia = array();
    $uploadDir = "C:/xampp/htdocs/valorantfanpage/content/"; 
    $allowedTypes = array("jpg", "jpeg", "png", "gif", "mp4", "webm");
    $maxFileSize = 5 * 1024 * 1024; 
    $mediaString = "";

    if (isset($_FILES["media"]) && is_array($_FILES["media"]["name"])) {
        foreach ($_FILES["media"]["name"] as $key => $name) {
            $tmpName = $_FILES["media"]["tmp_name"][$key];
            $error = $_FILES["media"]["error"][$key];

            if ($error === UPLOAD_ERR_OK) {
                $fileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($fileExtension, $allowedTypes) && $_FILES["media"]["size"][$key] <= $maxFileSize) {
                    $newFileName = bin2hex(random_bytes(8)) . '.' . $fileExtension;
                    $targetFile = $uploadDir . $newFileName;

                    if (move_uploaded_file($tmpName, $targetFile)) {
                        $uploadedMedia[] = $newFileName; // Add to array
                    } else {
                        echo "Error uploading file: " . $name . ". Please try again or contact the administrator.<br>"; 
                    }
                } else {
                    echo "Invalid file type or size for: " . $name . "<br>";
                }
            } else {
                echo "Upload error for file: " . $name . " (Error code: " . $error . "). Please try again or contact the administrator.<br>"; 
            }
        } 
    }

    $mediaString = implode(",", $uploadedMedia);

    // Prepare and Execute Query with Prepared Statement
    $sql = "INSERT INTO posts (title, content, media, created_by, tags_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $title, $content, $mediaString, $createdBy, $tags_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: social.php");
        exit();
    } else {
        echo "Error creating post: " . mysqli_error($connection);
    }

    mysqli_stmt_close($stmt);
}
mysqli_close($connection);
?>
