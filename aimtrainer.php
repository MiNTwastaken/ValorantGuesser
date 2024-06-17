<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aim Trainer</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #game-container {
            position: relative;
            width: 100%;
            height: 500px;
            border: 2px solid #000;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            cursor: crosshair;
        }

        #target {
            position: absolute;
            width: 50px;
            height: 50px;
            background-color: red;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s;
            display: none; /* Initially hide the target */
        }

        #target:hover {
            transform: scale(1.1);
        }

        #scoreboard {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333; /* Darker text for better readability */
        }
        
        h1 {
            color: #333; /* Darker heading color */
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Aim Trainer</h1>
        <div id="game-container" class="mb-3">
            <div id="target"></div>
        </div>
        <div id="scoreboard" class="mb-4">0</div>
        <button id="start-button" class="btn btn-primary">Start Game</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const target = document.getElementById('target');
        const scoreboard = document.getElementById('scoreboard');
        const startButton = document.getElementById('start-button');
        const gameContainer = document.getElementById('game-container');

        let score = 0;
        let startTime;
        let targetCount = 30;
        let gameStarted = false; // Flag to track if game is started

        target.addEventListener('click', () => {
            if (gameStarted) {
                score++;
                scoreboard.textContent = score;
                resetTarget();
                if (score >= targetCount) {
                    gameOver();
                }
            }
        });

        gameContainer.addEventListener('click', (event) => {
            if (event.target === gameContainer && gameStarted) { 
                target.style.backgroundColor = 'lightgray';
                score--; 
                scoreboard.textContent = score;
            }
        });

        function resetTarget() {
            const topPosition = Math.random() * (gameContainer.clientHeight - target.clientHeight);
            const leftPosition = Math.random() * (gameContainer.clientWidth - target.clientWidth);
            target.style.top = topPosition + 'px';
            target.style.left = leftPosition + 'px';
            target.style.backgroundColor = 'red';
        }

        function gameOver() {
            gameStarted = false; // Reset gameStarted flag
            const endTime = Date.now();
            if (startTime) {
                const totalTime = (endTime - startTime) / 1000;
                alert(`You hit ${score} targets in ${totalTime.toFixed(2)} seconds!`);
            } else {
                alert("Game hasn't started yet!");
            }
            score = 0;
            scoreboard.textContent = 0;
            startButton.disabled = false;
            target.style.display = 'none'; 
        }

        function startGame() {
            gameStarted = true; // Set gameStarted flag
            startTime = Date.now();
            score = 0;
            scoreboard.textContent = score;
            resetTarget();
            startButton.disabled = true;
            target.style.display = 'block';
        }

        startButton.addEventListener('click', startGame);
    </script>
</body>
</html>
