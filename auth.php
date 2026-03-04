<?php
session_start();

// Controllo base: se non sei loggato, vai al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Funzione di controllo permessi
function check_permission($ruoli_consentiti) {
    if (!in_array($_SESSION['ruolo'], $ruoli_consentiti)) {
        // Carica la pagina di errore stilizzata
        render_access_denied();
        exit;
    }
}

// Pagina di errore personalizzata
function render_access_denied() {
    echo '
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="style.css" rel="stylesheet">
        <title>Accesso Negato - Aura & Essenza</title>
        <style>
            body { background-color: #1a2a3a !important; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
            .error-card { background: #ffffff; border-radius: 15px; box-shadow: 0 15px 40px rgba(0,0,0,0.4); width: 100%; max-width: 450px; padding: 50px; text-align: center; }
            .logo-base { background-color: #1a2a3a; display: inline-block; padding: 20px 35px; border-radius: 15px; margin-bottom: 30px; }
            .login-logo { max-width: 180px; width: 100%; height: auto; }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="logo-base">
                <img src="logo.png" alt="Aura & Essenza" class="login-logo">
            </div>
            <h3 class="text-danger">Accesso Negato</h3>
            <p class="mb-4">Non hai i permessi necessari per visualizzare questa pagina su Aura & Essenza.</p>
            <a href="index.php" class="btn btn-gestione w-100">Torna alla Dashboard</a>
        </div>
    </body>
    </html>';
}
?>