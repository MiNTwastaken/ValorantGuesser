<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "valorantfanpage";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$quiz_id = $_GET['quiz_id'];
$sql = "SELECT * FROM quizzes WHERE id = $quiz_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Quiz not found.";
    $conn->close();
    exit();
}

$row = $result->fetch_assoc();

if ($_SESSION["username"] != $row["quizzler"]) {
    echo "You are not authorized to edit this quiz.";
    $conn->close();
    exit();
}

if (isset($_POST["delete_quiz"])) {
    $sql_delete = "DELETE FROM quizzes WHERE id=$quiz_id";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: minigames.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST["question"];
    $image_path = $row["image_path"];

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "C:/xampp/htdocs/valorantfanpage/img/quizzes/"; // Update the target directory path
        $image_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $image_name = uniqid() . '_' . time() . '.' . $image_extension;
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "img/quizzes/" . $image_name;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $answers = implode(',', $_POST["answers"]);
    $correct_answer = $_POST["correct_answer"];

    $sql = "UPDATE quizzes SET question='$question', answers='$answers', correct_answer=$correct_answer, image_path='$image_path' WHERE id=$quiz_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: edit_quiz.php?quiz_id=$quiz_id");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="styless.css">
    <style>
        .edit-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .edit-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .edit-form input[type="text"],
        .edit-form input[type="file"],
        .edit-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .edit-form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .edit-form button:hover {
            background-color: #3e8e41;
        }

        .delete-btn {
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .delete-btn:hover {
            background-color: #cc0000;
        }

        .btn-container {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <h1>Edit Quiz</h1>
        <form class="edit-form" method="post" enctype="multipart/form-data">
            <label for="question">Quiz Question:</label>
            <input type="text" id="question" name="question" value="<?php echo $row['question']; ?>" required>
            
            <label for="image">Quiz Image:</label>
            <input type="file" id="image" name="image">
            
            <img src="<?php echo $row['image_path']; ?>" alt="Current Image" style="max-width: 100%; height: auto; margin-bottom: 10px;">
            
            <label for="answers">Answers:</label>
            <?php
            $answers = explode(',', $row['answers']);
            foreach ($answers as $index => $answer) {
                echo '<input type="text" name="answers[]" value="' . $answer . '" required>';
            }
            ?>
            
            <label for="correct_answer">Correct Answer:</label>
            <select name="correct_answer" id="correct-answer" required>
                <?php
                foreach ($answers as $index => $answer) {
                    $selected = ($index == $row['correct_answer']) ? 'selected' : '';
                    echo '<option value="' . $index . '" ' . $selected . '>Answer ' . ($index + 1) . '</option>';
                }
                ?>
            </select>
            
            <div class="btn-container">
                <button type="submit">Update Quiz</button>
                <button type="submit" name="delete_quiz" class="delete-btn">Delete Quiz</button>
            </div>
        </form>
    </div>
</body>
</html>
