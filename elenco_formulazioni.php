<?php
require_once 'auth.php'; 
require_once 'db.php';

// 1. Controllo Permessi: Il magazziniere NON può accedere a questa pagina
if ($_SESSION['ruolo'] == 'magazziniere') {
    render_access_denied();
    exit;
}

// 2. LOGICA AZIONI RAPIDE
// Azioni per Controllo Qualità
if ($_SESSION['ruolo'] == 'controllo_qualita' && isset($_GET['azione']) && in_array($_GET['azione'], ['approva', 'rifiuta']) && isset($_GET['id'])) {
    $nuovo_stato = ($_GET['azione'] == 'approva') ? 'approvata' : 'rifiutata';
    $stmt = $pdo->prepare("UPDATE formulazioni SET stato = ? WHERE id = ?");
    $stmt->execute([$nuovo_stato, $_GET['id']]);
    header("Location: elenco_formulazioni.php");
    exit;
}

// Azione Eliminazione (Solo Admin)
if ($_SESSION['ruolo'] == 'admin' && isset($_GET['azione']) && $_GET['azione'] == 'elimina' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM formulazioni WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header("Location: elenco_formulazioni.php");
    exit;
}

// 3. Recupero dati
$formulazioni = $pdo->query("SELECT f.*, n.nome, n.cognome FROM formulazioni f JOIN nasi n ON f.id_naso = n.id ORDER BY f.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Elenco Formulazioni</title>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container mt-4">
        <h3>Gestione Formulazioni</h3>
        
        <table class="table mt-4 align-middle bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Naso</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($formulazioni as $f): ?>
                <tr>
                    <td><?php echo htmlspecialchars($f['nome_profumo']); ?></td>
                    <td><?php echo htmlspecialchars($f['nome'].' '.$f['cognome']); ?></td>
                    <td>
                        <?php 
                        $badge_class = 'secondary';
                        if ($f['stato'] == 'in_lavorazione') $badge_class = 'primary';
                        elseif ($f['stato'] == 'test_in_corso') $badge_class = 'warning';
                        elseif ($f['stato'] == 'approvata') $badge_class = 'success';
                        elseif ($f['stato'] == 'rifiutata') $badge_class = 'danger';
                        ?>
                        <span class="badge bg-<?php echo $badge_class; ?>">
                            <?php echo ucwords(str_replace('_', ' ', htmlspecialchars($f['stato']))); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($_SESSION['ruolo'] == 'controllo_qualita' && $f['stato'] == 'test_in_corso'): ?>
                            <a href="?azione=approva&id=<?php echo $f['id']; ?>" class="btn btn-sm btn-success">Approva</a>
                            <a href="?azione=rifiuta&id=<?php echo $f['id']; ?>" class="btn btn-sm btn-danger">Rifiuta</a>
                        <?php endif; ?>
                        
                        <a href="dettaglio_formulazione.php?id=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-dark">Dettagli</a>
                        
                        <?php if ($_SESSION['ruolo'] == 'admin'): ?>
                            <a href="?azione=elimina&id=<?php echo $f['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Sei sicuro di voler eliminare questa formulazione? L\'operazione è irreversibile.');">
                               Elimina
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>