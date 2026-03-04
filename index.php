<?php 
session_start(); 

// Controllo sicurezza: se l'utente non è loggato, reindirizza subito al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Aura & Essenza - Dashboard Laboratorio</title>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container mt-5">
        <div class="p-5 mb-4 bg-light rounded-3 shadow-sm border">
            <div class="container-fluid py-5 text-center">
                <h1 class="display-5 fw-bold">Gestione Laboratorio</h1>
                <p class="col-md-8 mx-auto fs-4 text-muted">
                    Benvenuto nel sistema di tracciamento formulazioni e magazzino essenze.
                </p>
            </div>
        </div>

        <div class="row text-center mt-4">
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] !== 'controllo_qualita'): ?>
            <div class="col-md-4 mb-3">
                <div class="card p-4 h-100 shadow-sm">
                    <h4>Magazzino Essenze</h4>
                    <p>Gestisci, traccia e cataloga le materie prime.</p>
                    <a href="gestisci_essenze.php" class="btn btn-gestione mt-2">Apri Magazzino</a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] !== 'magazziniere'): ?>
            <div class="col-md-4 mb-3">
                <div class="card p-4 h-100 shadow-sm">
                    <h4>Formulazioni</h4>
                    <p>Consulta le ricette esistenti o crea una nuova formula.</p>
                    <div class="d-grid gap-2 mt-2">
                        <a href="elenco_formulazioni.php" class="btn btn-success">Elenco Formulazioni</a>
                        
                        <?php if ($_SESSION['ruolo'] !== 'controllo_qualita'): ?>
                            <a href="nuova_formulazione.php" class="btn btn-gestione">Crea Nuova Formula</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'admin'): ?>
            <div class="col-md-4 mb-3">
                <div class="card p-4 h-100 shadow-sm">
                    <h4>Team Nasi</h4>
                    <p>Anagrafica dei professionisti del laboratorio.</p>
                    <a href="gestisci_nasi.php" class="btn btn-gestione mt-2">Gestisci Team</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>