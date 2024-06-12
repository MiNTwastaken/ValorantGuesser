<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aim Trainer</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1>Aim Trainer</h1>
    <div id="game-container">
        <div id="target"></div>
    </div>
    <div id="scoreboard">0</div>
    <button id="start-button">Start Game</button>
    <script>
        const target = document.getElementById('target');
        const scoreboard = document.getElementById('scoreboard');
        const startButton = document.getElementById('start-button');
        let score = 0;
        let startTime; 
        let targetCount = 30; 

        target.addEventListener('click', () => {
        score++;
        scoreboard.textContent = score;
        resetTarget();
        if (score >= targetCount) {
            gameOver();
        }
        });

        function resetTarget() {
        let topPosition = Math.random() * (100 - target.clientHeight) + '%';
        let leftPosition = Math.random() * (100 - target.clientWidth) + '%';
        target.style.top = topPosition;
        target.style.left = leftPosition;
        target.style.backgroundColor = 'red';
        }

        function gameOver() {
        const endTime = Date.now();
        if (startTime) {
            const totalTime = (endTime - startTime) / 1000; 
            alert(`You hit ${score} targets in ${totalTime.toFixed(2)} seconds!`);
        } else {
            alert("Game hasn't started yet!"); 
        }
        score = 0;
        startButton.disabled = false; 
        }

        function startGame() {
        startTime = Date.now(); 
        score = 0;
        scoreboard.textContent = score; 
        resetTarget();
        startButton.disabled = true;
        }

        startButton.addEventListener('click', startGame);

        document.addEventListener('click', (event) => {
        if (event.target !== startButton) {
            target.style.backgroundColor = 'lightgray';
            event.preventDefault();
        }
        });
    </script>
</body>
</html>
