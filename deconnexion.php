<?php
// 1. On récupère la session en cours
session_start();

// 2. On vide toutes les variables de la session
$_SESSION = array();

// 3. On détruit complètement la session côté serveur
session_destroy();

// 4. On redirige l'utilisateur vers la page d'accueil en tant que simple visiteur
header("Location: index.php");
exit;
?>
