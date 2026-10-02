<?php
// new/config/database.php
$host = 'localhost';
$dbname = 'ultrack'; // Le nom exact de votre base
$username = 'root';
$password = ''; // Laissez vide si vous n'avez pas de mot de passe XAMPP

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données.");
}
?>