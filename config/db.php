<?php
// Configuration de la base de données
$host = '192.168.132.203';
$dbname = 'Q230181'; // ⚠️ REMPLACE par le nom exact de ta base dans PhpMyAdmin
$username = 'Q230181';    // Par défaut sur XAMPP/WAMP. Sur MAMP (Mac), c'est souvent 'root' aussi.
$password = '82e7573158686c566c29ce5bc9269fae5142f211';        // Par défaut vide sur XAMPP/WAMP. Sur MAMP, c'est souvent 'root'.

try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Configuration pour afficher les erreurs SQL proprement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configuration pour récupérer les données sous forme de tableau associatif par défaut
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si la connexion échoue, on arrête la page et on affiche l'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>