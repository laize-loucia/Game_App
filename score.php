<?php
// On désactive l'affichage des erreurs pour ne pas polluer le JSON
ini_set('display_errors', 0); 

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../app/model/AuthModel.php';
require_once __DIR__ . '/../app/model/ScoreModel.php';
require_once __DIR__ . '/../app/model/LogModel.php';

// Nettoyage du buffer pour éviter les espaces blancs parasites
if (ob_get_length()) ob_clean(); 
header('Content-Type: application/json');

if (!AuthModel::estConnecte()) {
    echo json_encode(['succes' => false, 'message' => 'Non connecté']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = isset($_POST['score']) ? (int)$_POST['score'] : 0;
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['succes' => false, 'message' => 'Adresse email invalide.']);
        exit();
    }

    if ($score < SCORE_MIN || $score > SCORE_MAX) {
        echo json_encode(['succes' => false, 'message' => 'Score invalide.']);
        exit();
    }

    $resultat = ScoreModel::envoyerEmail($score, $email);
    echo json_encode([
        'succes' => $resultat, 
        'message' => $resultat ? 'Email envoyé !' : 'Échec de l\'envoi de l\'email.'
    ]);
    exit();
}

echo json_encode(['succes' => false, 'message' => 'Requête invalide.']);