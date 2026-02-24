<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../app/model/AuthModel.php';
require_once __DIR__ . '/../app/model/ScoreModel.php';

// Test manuel à placer après les require_once
LogModel::write('INFO', 'Test de création du fichier log');

if (!AuthModel::estConnecte()) {
    header('Location: index.php');
    exit();
}

$pseudo  = isset($_SESSION['pseudo']) ? htmlspecialchars($_SESSION['pseudo']) : 'Joueur';
$message = "";
$score   = 0;
$envoye  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = isset($_POST['score']) ? (int)$_POST['score'] : 0;

    if ($score < SCORE_MIN || $score > SCORE_MAX) {
        LogModel::write('WARNING', 'Score invalide reçu : ' . $score);
        $message = "Score invalide.";
    } else {
        ScoreModel::envoyerEmail($score);
        $message = "Votre score de $score a été envoyé par email !";
        $envoye  = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Score - Snake Game</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/style.css" />
</head>

<body>

  <!-- ══ SCREEN GAMEOVER ══ -->
  <div id="screen-gameover" class="screen">
    <div class="trophy-circle">🏆</div>
    <h1 class="gameover-title">Game Over</h1>
    <p class="gameover-subtitle" id="gameover-subtitle">Continue de t'entraîner ! 💪</p>

    <div class="score-card">
      <p class="score-joueur-label">Joueur</p>
      <p class="score-joueur-nom" id="go-pseudo">—</p>

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

      <div class="score-grid-3">
        <div class="score-box">
          <div class="score-box-label">Pommes</div>
          <div class="score-box-val sm val-rouge" id="go-pommes">0</div>
        </div>
        <div class="score-box">
          <div class="score-box-label">Longueur</div>
          <div class="score-box-val sm val-bleu" id="go-longueur">3</div>
        </div>
        <div class="score-box">
          <div class="score-box-label">Points/Pomme</div>
          <div class="score-box-val sm val-bleu">10</div>
        </div>
      </div>

      <div class="progression-box">
        <div class="progression-titre">📈 Progression</div>
        <div class="progression-bar-bg">
          <div class="progression-bar" id="go-prog-bar" style="width:5%"></div>
        </div>
        <p class="progression-texte" id="go-prog-txt">20 points pour devenir Intermédiaire</p>
      </div>
    </div>

    <script>
      afficherScoreFinal(<?= $score ?>, "<?= $pseudo ?>");
    </script>

    <div class="gameover-btns">
      <button class="btn-green" onclick="rejouer()">↺ &nbsp; Rejouer</button>
      <button class="btn-secondary" onclick="deconnecter()">↪ &nbsp; Changer de joueur</button>
    </div>

    <p class="gameover-astuce" id="go-astuce">💡 Astuce : Planifiez vos mouvements à l'avance pour obtenir un meilleur score !</p>
  </div>
  </div>

  <div class="gameover-btns">
    <a href="index.php" class="btn-green">↺ &nbsp; Rejouer</a>
    <a href="logout.php" class="btn-secondary">↪ &nbsp; Déconnexion</a>
  </div>

</body>
</html>
