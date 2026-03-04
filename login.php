<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db.php';

$errore = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_utente = $_POST['id_utente'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utenti WHERE id = ?");
    $stmt->execute([$id_utente]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['ruolo'] = $user['ruolo'];
        $_SESSION['id_naso'] = $user['id_naso']; // <--- QUESTA RIGA ERA IL PROBLEMA!
        
        // Controllo per password di default
        if (password_verify('default', $user['password'])) {
            header("Location: cambio_password.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $errore = "ID o password non corretti.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Login - Aura & Essenza</title>
    <style>
        body { background-color: #1a2a3a !important; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-card { background: #ffffff; border-radius: 15px; box-shadow: 0 15px 40px rgba(0,0,0,0.4); width: 100%; max-width: 450px; padding: 50px; text-align: center; }
        .logo-base { background-color: #1a2a3a; display: inline-block; padding: 30px 45px; border-radius: 15px; margin-bottom: 40px; }
        .login-logo { max-width: 220px; width: 100%; height: auto; }
        .form-label { font-size: 1.1rem !important; font-weight: 600; }
        .form-control { padding: 12px !important; font-size: 1.1rem !important; }
        .btn-gestione { padding: 15px !important; font-size: 1.1rem !important; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-base">
            <img src="logo.png" alt="Aura & Essenza" class="login-logo">
        </div>
        <?php if ($errore != "") { echo '<div class="alert alert-danger p-3 mb-4 text-center" style="font-size: 1rem;">'.$errore.'</div>'; } ?>
        <form method="POST" class="text-start">
            <div class="mb-4">
                <label class="form-label">ID Utente</label>
                <input type="text" name="id_utente" class="form-control" required autocomplete="off">
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-gestione w-100">Accedi al Sistema</button>
        </form>
    </div>
</body>
</html>