<?php
require_once '../config.php';

// Vérifie que le fichier de log est accessible
$logOk = is_writable(LOG_FILE);

// Vérifie que la session fonctionne
session_start();
$sessionOk = session_status() === PHP_SESSION_ACTIVE;

$status = ($logOk && $sessionOk) ? 'OK' : 'ERREUR';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Health Check</title>
</head>
<body>
    <h1>Status : <?= $status ?></h1>
    <ul>
        <li>Session : <?= $sessionOk ? ' OK' : ' NOTOK' ?></li>
        <li>Logs : <?= $logOk ? ' OK' : ' NOTOK' ?></li>
        <li>PHP :  <?= phpversion() ?></li>
    </ul>
</body>
</html>