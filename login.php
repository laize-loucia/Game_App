<?php
session_start();
require_once '../config.php';

// Déjà connecté → on redirige directement vers le jeu
if (isset($_SESSION['connecte']) && $_SESSION['connecte'] === true) {
    header('Location: index.php');
    exit();
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo   = isset($_POST['pseudo'])   ? trim($_POST['pseudo'])   : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';

    // Vérification mot de passe
    if ($password !== MOT_DE_PASSE) {
        $erreur = "Mot de passe incorrect.";
    }
    // Vérification pseudo
    elseif (strlen($pseudo) < 2 || strlen($pseudo) > 20) {
        $erreur = "Pseudo invalide. Minimum 2 caractères, maximum 20.";
    }
    // Tout est bon → on connecte
    else {
        session_regenerate_id(true);
        $_SESSION['connecte'] = true;
        $_SESSION['pseudo']   = htmlspecialchars($pseudo);
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion - Snake Game</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>

  <div class="login-top">
   <img src="assets/snake.png" alt="Logo Snake Game" weight=100 />
    <h1 class="login-title">Snake Game</h1>
    <p class="login-subtitle">Préparez-vous à jouer au classique !</p>
  </div>

  <div class="login-card">
    <p class="login-card-title">Bienvenue</p>

    <?php if ($erreur): ?>
      <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST">

      <!-- Pseudo -->
      <label class="field-label" for="pseudo">Votre pseudo</label>
      <div class="input-wrapper" style="margin-bottom:0.4rem;">
        <span class="input-icon">👤</span>
        <input type="text" id="pseudo" name="pseudo"
               placeholder="Votre pseudo"
               value="<?= isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : '' ?>"
               minlength="2" maxlength="20" required autocomplete="off" />
      </div>
      <p class="input-hint">Minimum 2 caractères, maximum 20</p>

      <!-- Mot de passe -->
      <label class="field-label" for="password">Mot de passe</label>
      <div class="input-wrapper" style="margin-bottom:1.5rem;">
        <span class="input-icon">🔒</span>
        <input type="password" id="password" name="password"
               placeholder="Mot de passe" required />
      </div>

      <button type="submit" class="btn-submit">
        Commencer à jouer &nbsp;→
      </button>

    </form>
  </div>

  <p class="login-footer">Créé avec PHP &amp; Canvas • © 2026</p>

</body>
</html>