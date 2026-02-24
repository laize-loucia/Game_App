/*Snake Game*/
const BOX        = 30; //taille d'une case en pixels
const COLS       = 13; // nombre de colonnes (largeur du canvas / BOX)
const ROWS       = 13; // nombre de lignes (hauteur du canvas / BOX)
const GAME_SPEED = 100; // vitesse du jeu
const pt      = 1;

let bestScore    = 0;
let score        = 0;
let pommes       = 0;
let snake, direction, nextDir, food, gameLoop, gameStarted;

const canvas = document.getElementById('gameCanvas');
const ctx    = canvas.getContext('2d');

// Lance le jeu au chargement de la page
initJeu();

/* ─── Init ─── */
function initJeu() {
  clearInterval(gameLoop);
  score       = 0;
  pommes      = 0;
  gameStarted = false;
  direction   = 'RIGHT';
  nextDir     = 'RIGHT';

  const mx = Math.floor(COLS / 2) * BOX;
  const my = Math.floor(ROWS / 2) * BOX;

  snake = [
    { x: mx,         y: my },
    { x: mx - BOX,   y: my },
    { x: mx - BOX*2, y: my }
  ];

  food = spawnFood();

  document.getElementById('score-display').textContent      = 0;
  document.getElementById('best-score-display').textContent = bestScore;
  document.getElementById('overlay-start').style.display    = 'flex';
  document.getElementById('overlay-gameover').style.display = 'none';

  // Remettre l'écran jeu visible, cacher le game over
  document.getElementById('screen-game').style.display = 'block';
  document.getElementById('screen-gameover').classList.remove('visible');

  dessiner();
}

function rejouer() { initJeu(); }

/* ─── Nourriture ─── */
function spawnFood() {
  let p;
  do {
    p = {
      x: Math.floor(Math.random() * COLS) * BOX,
      y: Math.floor(Math.random() * ROWS) * BOX
    };
  } while (snake.some(s => s.x === p.x && s.y === p.y));
  return p;
}

/* ─── Dessin ─── */
function dessiner() {
  const W = COLS * BOX;
  const H = ROWS * BOX;

  ctx.clearRect(0, 0, W, H);
  ctx.fillStyle = '#0a0a0a';
  ctx.fillRect(0, 0, W, H);

  // Grille subtile
  ctx.strokeStyle = 'rgba(255,255,255,0.03)';
  ctx.lineWidth   = 0.5;
  for (let i = 0; i <= COLS; i++) {
    ctx.beginPath(); ctx.moveTo(i*BOX, 0); ctx.lineTo(i*BOX, H); ctx.stroke();
  }
  for (let j = 0; j <= ROWS; j++) {
    ctx.beginPath(); ctx.moveTo(0, j*BOX); ctx.lineTo(W, j*BOX); ctx.stroke();
  }

  // Pomme (cercle rouge)
  ctx.fillStyle = '#f87171';
  ctx.beginPath();
  ctx.arc(food.x + BOX/2, food.y + BOX/2, BOX/2 - 3, 0, Math.PI * 2);
  ctx.fill();

  // Serpent (segments arrondis)
  snake.forEach((s, i) => {
    ctx.fillStyle = i === 0 ? '#00ff7f' : '#00cc60';
    rRect(s.x + 2, s.y + 2, BOX - 4, BOX - 4, 4);
    ctx.fill();
  });
}

function rRect(x, y, w, h, r) {
  ctx.beginPath();
  ctx.moveTo(x + r, y);
  ctx.lineTo(x + w - r, y);     ctx.quadraticCurveTo(x + w, y,     x + w, y + r);
  ctx.lineTo(x + w, y + h - r); ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
  ctx.lineTo(x + r, y + h);     ctx.quadraticCurveTo(x,     y + h, x,     y + h - r);
  ctx.lineTo(x, y + r);         ctx.quadraticCurveTo(x,     y,     x + r, y);
  ctx.closePath();
}

/* ─── Boucle de jeu ─── */
function tick() {
  direction = nextDir;
  const h = { x: snake[0].x, y: snake[0].y };

  if (direction === 'LEFT')  h.x -= BOX;
  if (direction === 'RIGHT') h.x += BOX;
  if (direction === 'UP')    h.y -= BOX;
  if (direction === 'DOWN')  h.y += BOX;

  // Collision mur
  if (h.x < 0 || h.x >= COLS * BOX || h.y < 0 || h.y >= ROWS * BOX) {
    finDeJeu(); return;
  }

  // Collision corps
  if (snake.some(s => s.x === h.x && s.y === h.y)) {
    finDeJeu(); return;
  }

  // Mange la pomme
  if (h.x === food.x && h.y === food.y) {
    score  += pt;
    pommes += 1;
    food    = spawnFood();
    document.getElementById('score-display').textContent = score;
    if (score > bestScore) {
      bestScore = score;
      document.getElementById('best-score-display').textContent = bestScore;
    }
  } else {
    snake.pop();
  }

  snake.unshift(h);
  dessiner();
}

/* ─── Fin de jeu ─── */
function finDeJeu() {
  clearInterval(gameLoop);

  // Overlay dans le canvas
  document.getElementById('overlay-gameover').style.display = 'flex';
  document.getElementById('overlay-score-txt').textContent  = 'Score : ' + score;

  // Transition vers écran score après 1.5s
  setTimeout(afficherScoreFinal, 1500);
}

function afficherScoreFinal() {
  document.getElementById('go-score').textContent    = score;
  document.getElementById('go-record').textContent   = bestScore;
  document.getElementById('go-pommes').textContent   = pommes;
  document.getElementById('go-longueur').textContent = snake.length;
  document.getElementById('form-score-val').value    = score;

  // Calcul rang & progression
  let rang = 'Débutant', prochainRang = 'Intermédiaire', seuil = 15, pct = 0;
  if      (score >= 50) { rang = 'Expert';        prochainRang = '';         seuil = 100; pct = 100; }
  else if (score >= 25)  { rang = 'Avancé';        prochainRang = 'Expert';   seuil =25; pct = ((score - 25) / 25)  * 100; }
  else if (score >= 15)  { rang = 'Intermédiaire'; prochainRang = 'Avancé';   seuil = 50;  pct = ((score - 15) / 35)  * 100; }
  else                   {                                                                  pct = (score / 15)         * 100; }

  document.getElementById('go-rang').textContent     = rang;
  document.getElementById('go-prog-bar').style.width = Math.max(3, pct) + '%';
  document.getElementById('go-prog-txt').textContent = prochainRang
    ? (seuil - score) + ' points pour devenir ' + prochainRang
    : '🏅 Rang maximum atteint !';

  const idx = score >= 100 ? 3 : score >= 50 ? 2 : score >= 20 ? 1 : 0;

  document.getElementById('gameover-subtitle').textContent = [
    "Continue de t'entraîner ! 💪",
    'Bonne partie ! 🎯',
    'Excellent score ! 🔥',
    'Tu es imbattable ! 🏆'
  ][idx];

  document.getElementById('go-astuce').textContent = [
    "💡 Planifiez vos mouvements à l'avance !",
    '💡 Longez les murs pour gagner de la place.',
    '💡 Pouvez-vous battre votre propre record ?'
  ][idx];

  // Basculer vers l'écran Game Over
  document.getElementById('screen-game').style.display = 'none';
  document.getElementById('screen-gameover').classList.add('visible');
}

// Dans game.js, à la fin de la fonction afficherScoreFinal()
function envoyerScoreAuServeur(scoreGenere) {
    const formData = new FormData();
    formData.append('score', scoreGenere);

    fetch('score.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log("Réponse du serveur :", data);
    })
    .catch(error => {
        console.error("Erreur lors de l'envoi :", error);
    });
}

// Appelle cette fonction dans afficherScoreFinal(score);

/* ─── Clavier ─── */
document.addEventListener('keydown', e => {
  const map = {
    ArrowLeft:  'LEFT',
    ArrowRight: 'RIGHT',
    ArrowUp:    'UP',
    ArrowDown:  'DOWN'
  };
  if (!map[e.key]) return;
  e.preventDefault();

  const opp = { LEFT: 'RIGHT', RIGHT: 'LEFT', UP: 'DOWN', DOWN: 'UP' };
  if (map[e.key] === opp[direction]) return;

  nextDir = map[e.key];

  // Démarre le jeu au premier appui
  if (!gameStarted) {
    gameStarted = true;
    document.getElementById('overlay-start').style.display = 'none';
    gameLoop = setInterval(tick, GAME_SPEED);
  }
});

/* ─── Instructions toggle ─── */
function toggleInstructions() {
  document.getElementById('instructions-panel').classList.toggle('visible');
}