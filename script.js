const dropdownBtns = document.querySelectorAll('.dropdown-btn');
const dropdownContents = document.querySelectorAll('.dropdown-content');

dropdownBtns.forEach((btn, index) => {
    btn.addEventListener('mouseover', () => {
        dropdownContents[index].style.display = 'block';
    });

    btn.addEventListener('mouseout', () => {
        dropdownContents[index].style.display = 'none';
    });
});


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
