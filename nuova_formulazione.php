<?php
// 1. Includiamo l'autenticazione
require_once 'auth.php'; 

// 2. Controllo: il controllo qualità e il magazziniere NON possono creare formulazioni
// Richiamiamo direttamente la funzione centralizzata che hai creato in auth.php
if ($_SESSION['ruolo'] == 'controllo_qualita' || $_SESSION['ruolo'] == 'magazziniere') {
    render_access_denied();
    exit;
}

// 3. Controllo specifico per l'id_naso
if (!isset($_SESSION['id_naso'])) {
    die("Errore: Profilo Naso non associato correttamente all'account. Contatta l'Admin.");
}

require_once 'db.php';

// Logica di salvataggio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO formulazioni (nome_profumo, id_naso, famiglia) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['nome_profumo'], 
        $_SESSION['id_naso'],
        $_POST['famiglia']
    ]);
    
    header("Location: dettaglio_formulazione.php?id=" . $pdo->lastInsertId());
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Nuova Formulazione</title>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container mt-4">
        <h3>Crea Nuova Formulazione</h3>
        <form method="POST" class="bg-white p-4 shadow-sm rounded border">
            <div class="mb-3">
                <label class="form-label">Nome del Profumo</label>
                <input type="text" name="nome_profumo" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Famiglia Olfattiva</label>
                <select name="famiglia" class="form-control" required>
                    <option value="fiorita">Fiorita</option>
                    <option value="legnosa">Legnosa</option>
                    <option value="orientale">Orientale</option>
                    <option value="fresca">Fresca</option>
                </select>
            </div>

            <p class="text-muted small">
                La formulazione verrà automaticamente associata al tuo profilo.
            </p>
            
            <button type="submit" class="btn btn-gestione w-100">Salva e Componi</button>
        </form>
    </div>
</body>
</html>