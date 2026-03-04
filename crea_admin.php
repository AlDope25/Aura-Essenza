<?php
require_once 'db.php';

try {
    // Dati richiesti
    $id_utente = 0; // ID 00000
    $password = password_hash('default', PASSWORD_DEFAULT); // Password 'default'
    $ruolo = 'admin';
    $id_naso = 99999;
    
    // Dati per la tabella nasi
    $nome = 'admin';
    $cognome = 'utente'; 
    $spec = 'Admin';

    // 1. Inserimento nella tabella nasi (il record 99999)
    $stmt1 = $pdo->prepare("INSERT INTO nasi (id, nome, cognome, specializzazione) VALUES (?, ?, ?, ?)");
    $stmt1->execute([$id_naso, $nome, $cognome, $spec]);

    // 2. Inserimento nella tabella utenti (ID 0)
    $stmt2 = $pdo->prepare("INSERT INTO utenti (id, password, ruolo, id_naso) VALUES (?, ?, ?, ?)");
    $stmt2->execute([$id_utente, $password, $ruolo, $id_naso]);

    echo "Utente Admin (ID: 0) creato con successo con password 'default'!";
} catch (PDOException $e) {
    echo "Errore durante la creazione (probabile record esistente): " . $e->getMessage();
}
?>