<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database Connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "valorantfanpage";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get logged-in user's username
    $quizzler = $_SESSION["username"];

    // Extract form data
    $question = $_POST["question"];
    $answers = implode(',', $_POST["answers"]); // Convert array to comma-separated string
    $correct_answer = $_POST["correct_answer"];
    
    // Upload image
    $image_path = "";
    if ($_FILES["image"]["error"] == 0) {
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]); // Generate unique image name
        $image_path = "img/quizzes/" . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    // Insert quiz data into the database
    $sql = "INSERT INTO quizzes (quizzler, question, answers, correct_answer, image_path) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $quizzler, $question, $answers, $correct_answer, $image_path);
    if ($stmt->execute()) {
        header("Location: minigames.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styless.css">
    <title>Create Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 0;
        }

        .content {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            margin-top: 50px;
        }

        h2 {
            text-align: center;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        textarea,
        input[type="text"],
        input[type="file"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        #answers-container input[type="text"] {
            margin-bottom: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        button:hover {
            background-color: #3e8e41;
        }

        #add-answer {
            margin-top: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <h2>Create Quiz</h2>
        <form id="quiz-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label for="question">Question:</label>
            <textarea name="question" rows="4" cols="50" required></textarea>
            
            <label for="image">Upload Image:</label>
            <input type="file" name="image" id="image" accept="image/*">
            
            <div id="answers-container">
                <label for="answers">Answers:</label>
                <input type="text" name="answers[]" required><br>
                <input type="text" name="answers[]" required><br>
                <input type="text" name="answers[]" required><br>
            </div>

            <button type="button" id="add-answer">Add Answer</button>

            <label for="correct_answer">Correct Answer:</label>
            <select name="correct_answer" id="correct-answer" required>
                <!-- Dropdown options will be added dynamically -->
            </select>

            <button type="submit" value="Create Quiz">Create Quiz</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            var maxAnswers = 10; // Maximum number of answers allowed
            var answerCount = 3; // Initial number of answer inputs

            // Initialize correct answer dropdown options
            for (var i = 0; i < answerCount; i++) {
                $("#correct-answer").append('<option value="' + i + '">Answer ' + (i + 1) + '</option>');
            }

            // Add answer input field
            $("#add-answer").click(function() {
                if (answerCount < maxAnswers) {
                    $("#answers-container").append('<input type="text" name="answers[]" required><br>');
                    answerCount++;

                    // Update correct answer dropdown options
                    $("#correct-answer").append('<option value="' + (answerCount - 1) + '">Answer ' + answerCount + '</option>');
                }
            });
        });
    </script>
</body>
</html>

