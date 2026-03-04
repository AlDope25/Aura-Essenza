<?php
// 1. Controllo sicurezza e permessi: solo Admin
require_once 'auth.php';
check_permission(['admin']); 

require_once 'db.php';

// Inserimento nuovo Naso e creazione utente associato
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aggiungi_naso'])) {
    $stmt = $pdo->prepare("INSERT INTO nasi (nome, cognome, specializzazione) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['cognome'], $_POST['specializzazione']]);
    $id_naso = $pdo->lastInsertId();

    $id_utente_casuale = rand(10000, 99999);
    $password_default = password_hash('default', PASSWORD_DEFAULT);

    $spec = $_POST['specializzazione'];
    $ruolo = ($spec == 'Admin') ? 'admin' : 
             (($spec == 'Magazziniere') ? 'magazziniere' : 
             (($spec == 'Controllo Qualità') ? 'controllo_qualita' : 'esperto'));
    
    $stmt_user = $pdo->prepare("INSERT INTO utenti (id, password, ruolo, id_naso) VALUES (?, ?, ?, ?)");
    $stmt_user->execute([$id_utente_casuale, $password_default, $ruolo, $id_naso]);

    $_SESSION['notifica_naso'] = "Naso aggiunto! ID accesso: <strong>$id_utente_casuale</strong>, Password temporanea: <strong>default</strong>.";

    header("Location: gestisci_nasi.php");
    exit;
}

// LOGICA RESET PASSWORD
if (isset($_GET['reset_password_id'])) {
    $id_naso = (int)$_GET['reset_password_id'];
    
    $stmt = $pdo->prepare("SELECT id FROM utenti WHERE id_naso = ?");
    $stmt->execute([$id_naso]);
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utente) {
        $new_pass = password_hash('default', PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE utenti SET password = ? WHERE id = ?");
        $update->execute([$new_pass, $utente['id']]);
        header("Location: gestisci_nasi.php?msg=reset_ok");
        exit;
    }
}

// Logica per rimuovere un Naso (con controllo sicurezza)
if (isset($_GET['id_elimina'])) {
    $id_naso_da_eliminare = (int)$_GET['id_elimina'];

    // 1. Contiamo quanti admin ci sono in totale
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM utenti WHERE ruolo = 'admin'");
    $totale_admin = $stmt_count->fetchColumn();

    // 2. Verifichiamo se il naso che stiamo eliminando è un admin
    $stmt_check = $pdo->prepare("SELECT ruolo FROM utenti WHERE id_naso = ?");
    $stmt_check->execute([$id_naso_da_eliminare]);
    $ruolo_da_eliminare = $stmt_check->fetchColumn();

    if ($ruolo_da_eliminare == 'admin' && $totale_admin <= 1) {
        // Se è l'ultimo admin, blocchiamo l'operazione
        $_SESSION['notifica_naso'] = "<div class='alert alert-danger'>Errore: Non puoi eliminare l'unico amministratore del sistema!</div>";
    } else {
        // Procediamo con l'eliminazione
        $pdo->prepare("DELETE FROM utenti WHERE id_naso = ?")->execute([$id_naso_da_eliminare]);
        $pdo->prepare("DELETE FROM nasi WHERE id = ?")->execute([$id_naso_da_eliminare]);
        $_SESSION['notifica_naso'] = "<div class='alert alert-success'>Naso eliminato con successo.</div>";
    }
    
    header("Location: gestisci_nasi.php");
    exit;
}

$nasi = $pdo->query("SELECT nasi.*, utenti.id as id_utente, utenti.ruolo 
                     FROM nasi 
                     LEFT JOIN utenti ON nasi.id = utenti.id_naso 
                     ORDER BY cognome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Gestione Team Nasi</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h3>Team: Nasi Professionisti</h3>
        
        <?php if (isset($_SESSION['notifica_naso'])): ?>
            <div class="mt-3">
                <?php echo $_SESSION['notifica_naso']; ?>
            </div>
            <?php unset($_SESSION['notifica_naso']); ?>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'reset_ok'): ?>
            <div class="alert alert-success mt-3">Password resettata con successo a "default".</div>
        <?php endif; ?>
        
        <form method="POST" class="row g-3 my-4 bg-white p-3 shadow-sm rounded border">
            <div class="col-md-3"><input type="text" name="nome" placeholder="Nome" class="form-control" required></div>
            <div class="col-md-3"><input type="text" name="cognome" placeholder="Cognome" class="form-control" required></div>
            <div class="col-md-4">
                <select name="specializzazione" class="form-control" required>
                    <option value="Admin">Admin</option>
                    <option value="Esperto Note Floreali">Esperto Note Floreali</option>
                    <option value="Esperto Note Legnose">Esperto Note Legnose</option>
                    <option value="Esperto Note Orientali">Esperto Note Orientali</option>
                    <option value="Esperto Note Fresche">Esperto Note Fresche</option>
                    <option value="Controllo Qualità">Controllo Qualità</option>
                    <option value="Magazziniere">Magazziniere</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" name="aggiungi_naso" class="btn btn-gestione w-100">Aggiungi</button></div>
        </form>

        <table class="table table-hover mt-3 bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Ruolo</th>
                    <th>ID</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nasi as $n): 
                    $ruolo_raw = $n['ruolo'] ?? '';
                    $ruolo_classe = ($ruolo_raw == 'admin') ? 'badge-admin' : 
                                   (($ruolo_raw == 'magazziniere') ? 'badge-magazziniere' : 
                                   (($ruolo_raw == 'controllo_qualita') ? 'badge-qualita' : 'badge-esperto'));
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($n['nome']); ?></td>
                    <td><?php echo htmlspecialchars($n['cognome']); ?></td>
                    <td>
                        <span class="badge <?php echo $ruolo_classe; ?>">
                            <?php echo htmlspecialchars($n['specializzazione']); ?>
                        </span>
                    </td>
                    <td><strong><?php echo $n['id_utente'] ?? 'N/A'; ?></strong></td>
                    <td>
                        <a href="?reset_password_id=<?php echo $n['id']; ?>" 
                           class="btn btn-warning btn-sm" 
                           onclick="return confirm('Resettare la password a \"default\"?');">
                           Reset Pwd
                        </a>
                        <a href="?id_elimina=<?php echo $n['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Eliminando il naso eliminerai anche il suo account. Procedere?');">
                           Rimuovi
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>