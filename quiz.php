<?php
session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "valorantfanpage";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch quiz data based on ID from URL
$quiz_data = null;
$message = "";
if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    $sql = "SELECT * FROM quizzes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $quiz_data = $result->fetch_assoc();
    } else {
        $message = "Quiz not found!";
    }
} else {
    $message = "Quiz ID not provided!";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($quiz_data)) {
    // Check answer
    $selected_answer = $_POST['answer'];
    $correct_answer = $quiz_data['correct_answer'];
    if ($selected_answer == $correct_answer) {
        $message = "Correct answer!";
    } else {
        $answers = explode(',', $quiz_data['answers']);
        $message = "Incorrect answer. The correct answer is: " . $answers[$correct_answer];
    }
}

// Close database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Minigame</title>
    <link rel="stylesheet" href="styless.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: white;
        }

        .quiz-container {
            background-color: #282828;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .quiz-message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #383838;
            border-radius: 5px;
        }

        .quiz-image img {
            max-width: 100%;
            max-height: 300px;
            height: auto;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .quiz-options {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .quiz-options label {
            margin-bottom: 10px;
            font-size: 18px;
        }

        .quiz-options input[type="radio"] {
            margin-right: 5px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #ff4500;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .navbar {
            background-color: red;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
        }
    </style>
    <script>
        function validateForm() {
            var selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (!selectedAnswer) {
                alert("Please select an answer.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <div class="quiz-container">
        <?php if (!empty($message)): ?>
            <div class="quiz-message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($quiz_data)): ?>
            <div class="quiz-image">
                <img src="<?php echo $quiz_data['image_path']; ?>" alt="Quiz Image">
            </div>
            <h3><?php echo $quiz_data['question']; ?></h3>
            <form method="post" onsubmit="return validateForm()">
                <div class="quiz-options">
                    <?php
                    $answers = explode(',', $quiz_data['answers']);
                    foreach($answers as $key => $answer) {
                        echo '<label><input type="radio" name="answer" value="'.$key.'">'.$answer.'</label>';
                    }
                    ?>
                </div>
                <button type="submit">Submit</button>
            </form>
        <?php else: ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
    <?php include 'navbar.php'; ?>
</body>
</html>
