<?php
session_start();
require_once '../config.php';
require_once __DIR__ . '/../app/model/AuthModel.php';

// Rediriger vers login si pas connecté
if (!AuthModel::estConnecte()) {
    header('Location: login.php');
    exit();
}

$pseudo = isset($_SESSION['pseudo']) ? htmlspecialchars($_SESSION['pseudo']) : 'Joueur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Snake Game</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>

  <!--Jeu-->
  <div id="screen-game" class="screen active">
    <nav class="navbar">
      <div class="navbar-logo">
        <img src="assets/snake.png" alt="Logo Snake Game" class="logo-img" /> Snake game
      </div>
      <div class="navbar-actions">
        <button class="btn-user">👤 <?= $pseudo ?></button>
        <button class="btn-logout" onclick="location.href='logout.php'" title="Déconnexion">↪ Déconnexion</button>
      </div>
    </nav>

    <div class="game-layout">

      <!-- Règles à gauche -->
      <div class="rules-panel">
        <h2>Règles du jeu :</h2>
        <strong>Objectif :</strong> Mangez les pommes rouges pour que le serpent grandisse et marque des points.<br/><br/>
        <strong>Contrôles :</strong> Utiliser les flèches directionnelles du clavier.<br/><br/>
        <strong>Game Over :</strong> Si vous touchez un mur ou le corps du serpent.<br/><br/>
        🏆 Chaque pomme vaut 1 point. Battez votre record !
      </div>

      <!-- Jeu au centre -->
      <div class="game-content">
        <div class="canvas-wrapper">
          <canvas id="gameCanvas" width="595" height="595"></canvas>

          <div class="game-overlay" id="overlay-start">
            <h3>Appuyez sur une flèche pour commencer</h3>
            <p>Utilisez les flèches directionnelles</p>
          </div>

          <div class="game-overlay" id="overlay-gameover" style="display:none;">
            <h3>Game Over!</h3>
            <p id="overlay-score-txt">Score : 0</p>
            <button class="overlay-btn" onclick="rejouer()">Rejouer</button>
          </div>
        </div>

        <!-- Score sous le canvas -->
        <div class="score-display">
          <div class="score-item">
            <div class="score-item-label">Score</div>
            <div class="score-item-val" id="score-display">0</div>
          </div>
          <div class="score-item">
            <div class="score-item-label">Meilleur Score</div>
            <div class="score-item-val best" id="best-score-display">0</div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!--fin du jeu-->
  <div id="screen-gameover" class="screen">
    <nav class="navbar">
      <div class="navbar-logo">
        <img src="assets/snake.png" alt="Logo Snake Game" class="logo-img" /> Snake game
      </div>
      <div class="navbar-actions">
        <button class="btn-user">👤 <?= $pseudo ?></button>
        <button class="btn-logout" onclick="location.href='logout.php'" title="Déconnexion">↪ Déconnexion</button>
      </div>
    </nav>

    <div class="trophy-circle">🏆</div>
    <h1 class="gameover-title">Game Over</h1>
    <p class="gameover-subtitle" id="gameover-subtitle">Continue de t'entraîner ! 💪</p>

    <div class="score-card">
      <p class="score-joueur-label">Joueur</p>
      <p class="score-joueur-nom"><?= $pseudo ?></p>

      <div class="score-grid-2">
        <div class="score-box">
          <div class="score-box-icon">🎯</div>
          <div class="score-box-label">Score</div>
          <div class="score-box-val val-vert" id="go-score">0</div>
        </div>
        <div class="score-box">
          <div class="score-box-icon">🏆</div>
          <div class="score-box-label">Record</div>
          <div class="score-box-val val-jaune" id="go-record">0</div>
        </div>
      </div>

      <div class="rang-box">
        <span style="font-size:1.3rem">🎮</span>
        <div>
          <div class="rang-label">Rang</div>
          <div class="rang-val" id="go-rang">Débutant</div>
        </div>
      </div>
    </div>

    <div class="gameover-btns">
      <button class="btn-green" onclick="rejouer()">↺ &nbsp; Rejouer</button>
      <button class="btn-mail" onclick="envoyerScoreMail()">📧 &nbsp; Recevoir mon score</button>
      <button class="btn-secondary" onclick="location.href='logout.php'">↪ &nbsp; Déconnexion</button>
    </div>
  </div>


  <script>
    const BOX        = 35;
    const COLS       = 17;
    const ROWS       = 17;
    const GAME_SPEED = 100;
    const PTS        = 1;
    const PSEUDO     = <?= json_encode($pseudo) ?>;

    let score = 0, pommes = 0;
    // Récupère le meilleur score sauvegardé pour ce pseudo
    let bestScore = parseInt(localStorage.getItem('bestScore_' + PSEUDO)) || 0;
    let snake, direction, nextDir, food, gameLoop, gameStarted;

    const canvas = document.getElementById('gameCanvas');
    const ctx    = canvas.getContext('2d');

    // Lance le jeu directement au chargement
    initJeu();

    function showScreen(id) {
      // Cacher tous les écrans
      document.getElementById('screen-game').style.display     = 'none';
      document.getElementById('screen-gameover').style.display = 'none';

      // Afficher le bon écran avec le bon display
      if (id === 'screen-game') {
        document.getElementById('screen-game').style.display = 'block';
      } else if (id === 'screen-gameover') {
        document.getElementById('screen-gameover').style.display = 'flex';
      }
    }

    function rejouer() {
      showScreen('screen-game');
      initJeu();
    }

    function initJeu() {
      clearInterval(gameLoop);
      score = 0; pommes = 0;
      gameStarted = false;
      direction = 'RIGHT'; nextDir = 'RIGHT';

      const mx = Math.floor(COLS / 2) * BOX;
      const my = Math.floor(ROWS / 2) * BOX;
      snake = [
        { x: mx, y: my },
      ];
      food = spawnFood();

      document.getElementById('score-display').textContent      = 0;
      document.getElementById('best-score-display').textContent = bestScore;
      document.getElementById('overlay-start').style.display    = 'flex';
      document.getElementById('overlay-gameover').style.display = 'none';
      showScreen('screen-game');
      setTimeout(dessiner, 10); // petit délai pour que le DOM soit visible avant de dessiner
    }

    // Génère une position pour la nourriture qui n'est pas sur le serpent
    function spawnFood() {
      let p;
      do {
        p = { x: Math.floor(Math.random()*COLS)*BOX, y: Math.floor(Math.random()*ROWS)*BOX };
      } while (snake.some(s => s.x===p.x && s.y===p.y));
      return p;
    }

    // Dessine le fond, la grille, la nourriture et le serpent
    function dessiner() {
      const W = COLS*BOX, H = ROWS*BOX;
      ctx.clearRect(0,0,W,H);
      ctx.fillStyle = '#0a0a0a';
      ctx.fillRect(0,0,W,H);

      ctx.strokeStyle = 'rgba(255,255,255,0.03)';
      ctx.lineWidth = 0.5;
      for (let i=0;i<=COLS;i++) { ctx.beginPath(); ctx.moveTo(i*BOX,0); ctx.lineTo(i*BOX,H); ctx.stroke(); }
      for (let j=0;j<=ROWS;j++) { ctx.beginPath(); ctx.moveTo(0,j*BOX); ctx.lineTo(W,j*BOX); ctx.stroke(); }

      ctx.fillStyle = '#ff0000';
      ctx.beginPath();
      ctx.arc(food.x+BOX/2, food.y+BOX/2, BOX/2-3, 0, Math.PI*2);
      ctx.fill();

      snake.forEach((s,i) => {
        ctx.fillStyle = i===0 ? 'green' : '#00cc60';
        rRect(s.x+2, s.y+2, BOX-4, BOX-4, 4);  
        ctx.fill();
      });
    }

    // Dessine un rectangle arrondi
    function rRect(x,y,w,h,r) {
      ctx.beginPath();
      ctx.moveTo(x+r,y);
      ctx.lineTo(x+w-r,y); ctx.quadraticCurveTo(x+w,y,x+w,y+r);
      ctx.lineTo(x+w,y+h-r); ctx.quadraticCurveTo(x+w,y+h,x+w-r,y+h);
      ctx.lineTo(x+r,y+h); ctx.quadraticCurveTo(x,y+h,x,y+h-r);
      ctx.lineTo(x,y+r); ctx.quadraticCurveTo(x,y,x+r,y);
      ctx.closePath();
    }

    // Logique de déplacement du serpent, gestion des collisions et de la nourriture
    function tick() {
      direction = nextDir;
      const h = { x: snake[0].x, y: snake[0].y };
      if (direction==='LEFT')  h.x -= BOX;
      if (direction==='RIGHT') h.x += BOX;
      if (direction==='UP')    h.y -= BOX;
      if (direction==='DOWN')  h.y += BOX;

      if (h.x<0 || h.x>=COLS*BOX || h.y<0 || h.y>=ROWS*BOX) { finDeJeu(); return; }
      if (snake.slice(1).some(s=>s.x===h.x && s.y===h.y)) { finDeJeu(); return; }
      if (h.x===food.x && h.y===food.y) {
        score += PTS; pommes++;
        food = spawnFood();
        document.getElementById('score-display').textContent = score;
        if (score > bestScore) {
          bestScore = score;
          document.getElementById('best-score-display').textContent = bestScore;
          // Sauvegarde le meilleur score dans le localStorage lié au pseudo
          localStorage.setItem('bestScore_' + PSEUDO, bestScore);
        }
      } else { snake.pop(); }

      snake.unshift(h);
      dessiner();
    }

    // Affiche l'écran de fin de jeu et le score final
    function finDeJeu() {
      clearInterval(gameLoop);
      document.getElementById('overlay-gameover').style.display = 'flex';
      document.getElementById('overlay-score-txt').textContent  = 'Score : ' + score;
      setTimeout(() => {
        afficherScoreFinal();
        showScreen('screen-gameover');
      }, 1500);
    }

    function afficherScoreFinal() {
      document.getElementById('go-score').textContent  = score;
      document.getElementById('go-record').textContent = bestScore;

      let rang = 'Débutant';
      if      (score >= 50) rang = 'Expert';
      else if (score >= 30) rang = 'Avancé';
      else if (score >= 15) rang = 'Intermédiaire';
      document.getElementById('go-rang').textContent = rang;

      const idx = score>=100?3 : score>=50?2 : score>=20?1 : 0;
      document.getElementById('gameover-subtitle').textContent =
        ["Continue de t'entraîner ! 💪",'Bonne partie ! 🎯','Excellent score ! 🔥','Tu es imbattable ! 🏆'][idx];
    }

    document.addEventListener('keydown', e => {
      const map = { ArrowLeft:'LEFT', ArrowRight:'RIGHT', ArrowUp:'UP', ArrowDown:'DOWN' };
      if (!map[e.key]) return;
      e.preventDefault();
      const opp = { LEFT:'RIGHT', RIGHT:'LEFT', UP:'DOWN', DOWN:'UP' };
      if (map[e.key] === opp[direction]) return;
      nextDir = map[e.key];
      if (!gameStarted) {
        gameStarted = true;
        document.getElementById('overlay-start').style.display = 'none';
        gameLoop = setInterval(tick, GAME_SPEED);
      }
    });

    // Ouvre le client mail avec le score pré-rempli
    function envoyerScoreMail() {
      const sujet = encodeURIComponent('Mon score Snake Game 🐍');
      const corps = encodeURIComponent(
        'Bonjour !\n\n' +
        'Voici mon score pour la partie Snake :\n\n' +
        '🎯 Score : ' + score + ' points\n' +
        '🏆 Meilleur score : ' + bestScore + ' points\n' +
        '👤 Joueur : ' + PSEUDO + '\n\n' +
        '📅 Date : ' + new Date().toLocaleDateString('fr-FR') + '\n\n' +
        'Bonne chance pour la prochaine partie !'
      );
      window.location.href = 'mailto:?subject=' + sujet + '&body=' + corps;
    }

  </script>

</body>
</html>