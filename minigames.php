<!DOCTYPE html>
<html>
<head>
    <title>Valorant Minigames</title>
    <link rel="stylesheet" href="styless.css">
    <style>
        .user-quizzes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .user-quiz {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            text-align: center;
        }

        .user-quiz img {
            max-width: 100%;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
        }

        .user-quiz p {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }

        .user-quiz .author {
            font-size: 14px;
            color: #666;
            margin-top: -10px;
        }

        .user-quiz a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
            transition: background-color 0.2s ease-in-out;
        }

        .user-quiz a:hover {
            background-color: #0056b3;
        }

        .edit-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FFA500;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
            transition: background-color 0.2s ease-in-out;
        }

        .edit-btn:hover {
            background-color: #CC8400;
        }

        .leaderboard-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff4500;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            transition: background-color 0.2s ease-in-out;
        }

        .leaderboard-btn:hover {
            background-color: #cc3700;
        }

        .create-quiz-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            transition: background-color 0.2s ease-in-out;
        }

        .create-quiz-btn:hover {
            background-color: #3e8e41;
        }

        .content {
            padding: 20px;
        }

        .minigame-grid {
            margin-top: 20px;
        }

        .minigame-grid .minigame {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            text-align: center;
            margin-bottom: 20px;
        }

        .minigame-grid .minigame h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .minigame-grid .minigame p {
            font-size: 16px;
        }

        .minigame-grid .minigame a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            transition: background-color 0.2s ease-in-out;
        }

        .minigame-grid .minigame a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <script src="script.js"></script>
    <?php session_start(); ?>
    <?php include 'navbar.php'; ?>
    <div class="content">
        <h2>User Created Quizzes</h2>

        <?php
        if (isset($_SESSION["username"])) {
            echo '<div style="text-align: center;">';
            echo '<a href="create_quiz.php" class="create-quiz-btn">Create a User Quiz</a>';
            echo '</div>';
        } else {
            echo '<div class="login-prompt">';
            echo '<p>To create quizzes, please <a href="login.php">log in</a> or <a href="register.php">register</a>.</p>';
            echo '</div>';
        }
        ?>

        <div class="user-quizzes">
            <?php
            // Fetch user-created quizzes from the database
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "valorantfanpage";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT id, question, image_path, quizzler FROM quizzes";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="user-quiz">';
                    echo '<img src="' . $row["image_path"] . '" alt="Quiz Image">';
                    echo '<p>Question: ' . $row["question"] . '</p>';
                    echo '<p class="author">Created by: ' . $row["quizzler"] . '</p>';
                    echo '<a href="quiz.php?quiz_id=' . $row["id"] . '">Play Quiz</a>';

                    if (isset($_SESSION["username"]) && $_SESSION["username"] == $row["quizzler"]) {
                        echo '<a href="edit_quiz.php?quiz_id=' . $row["id"] . '" class="edit-btn">Edit Quiz</a>';
                    }

                    echo '</div>';
                }
            } else {
                echo "No user-created quizzes available.";
            }
            $conn->close();
            ?>
        </div>
        <h1>Minigames offered by us!</h1>
        <p>Test your Valorant skills and knowledge with these fun minigames!</p>
        <div class="minigame-grid">
            <div class="minigame">
                <h2>Daily Quiz</h2>
                <p>Guessing game about Valorant to test your knowledge</p>
                <p>A global one time daily event for all</p>
                <a href="dailychallenge.php">Play Daily Quiz</a>
            </div>
            <div class="minigame">
                <h2>One Shot</h2>
                <p>Sharpen your aim and reflexes by shooting targets the fastest</p>
                <a href="aimtrainer.php">Play One Shot</a>
            </div>
            <div class="minigame">
                <h2>Free Play</h2>
                <p>Boundless guessing game about Valorant to test your knowledge</p>
                <p>Choose your category, choose a new prompt, earn your experience</p>
                <a href="freeplay.php">Play Free Play</a>
            </div>
        </div>
        <div style="text-align: center;">
            <a href="leaderboard.php" class="leaderboard-btn">View Leaderboard</a>
        </div>
    </div>
</body>
</html>
