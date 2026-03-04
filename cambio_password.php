<?php
session_start();
require_once 'db.php';

// Protezione: se non sei loggato, torna al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuova_password = $_POST['nuova_password'];
    $conferma_password = $_POST['conferma_password'];

    if ($nuova_password === $conferma_password) {
        $hash = password_hash($nuova_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utenti SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $_SESSION['user_id']]);
        
        // Reindirizzamento automatico alla Dashboard
        header("Location: index.php");
        exit;
    } else {
        $messaggio = '<div class="alert alert-danger p-2 mb-3 text-center" style="font-size: 0.9rem;">Le password non coincidono.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Cambio Password - Aura & Essenza</title>
    <style>
        body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background: #fff; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .logo-base { background-color: #1a2a3a; display: inline-block; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .login-logo { max-width: 220px; width: 100%; height: auto; }
        .form-label { font-size: 1.1rem !important; font-weight: 600; }
        .form-control { padding: 12px !important; font-size: 1.1rem !important; }
        .btn-gestione { padding: 15px !important; font-size: 1.1rem !important; background-color: #1a2a3a; color: #fff; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-base">
            <img src="logo.png" alt="Aura & Essenza" class="login-logo">
        </div>

        <h4 class="mb-4">Nuova Password</h4>
        <p class="text-muted mb-4">Imposta una password sicura per il tuo account.</p>
        
        <?php echo $messaggio; ?>
        
        <form method="POST" class="text-start">
            <div class="mb-4">
                <label class="form-label">Nuova Password</label>
                <input type="password" name="nuova_password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Conferma Password</label>
                <input type="password" name="conferma_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-gestione w-100">Aggiorna Password</button>
        </form>
    </div>

</body>
</html>