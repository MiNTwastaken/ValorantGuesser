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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .quiz-container {
            background-color: #282828;
            padding: 2rem;
            border-radius: 0.625rem;
            text-align: center;
            max-width: 37.5rem;
            width: 100%;
            margin: 2rem auto;
            color: #ffffff; /* Changed text color to white for better contrast */
        }

        .quiz-message {
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #383838;
            border-radius: 0.3125rem;
            color: #ffffff; /* Changed text color to white for better contrast */
        }

        .quiz-image img {
            max-width: 100%;
            max-height: 18.75rem;
            height: auto;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
        }

        .quiz-options label {
            margin-bottom: 1rem;
            font-size: 1.125rem;
            display: block;
            color: #ffffff; /* Changed text color to white for better contrast */
        }

        .quiz-options input[type="radio"] {
            margin-right: 0.3125rem;
        }

        button[type="submit"] {
            padding: 0.625rem 1.25rem;
            background-color: #ff4500;
            color: white;
            border: none;
            border-radius: 0.3125rem;
            cursor: pointer;
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
    <?php include 'navbar.php'; ?>
    <div class="container">
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
