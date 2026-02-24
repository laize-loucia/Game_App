<?php
session_start();
require_once '../config.php';

// Détruit toutes les variables de session
$_SESSION = array();

// Détruit le session de cookeis si nécessaire
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruit la session
session_destroy();

// Redirige vers la page de connexion
header('Location: login.php');
exit();
?>
