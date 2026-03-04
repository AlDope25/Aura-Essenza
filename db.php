<?php
$host = 'localhost';
$db   = 'aura_essenza';
$user = 'root';
$pass = ''; 

try {
    // Usiamo $pdo come variabile
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Non usiamo die(), così il test non crasha, ma logghiamo l'errore
    error_log("Errore connessione: " . $e->getMessage());
    $pdo = null;
}
?>