<?php
require_once 'auth.php'; 
require_once 'db.php';

$id_f = $_GET['id'] ?? null;
if (!$id_f) { header("Location: index.php"); exit; }

// 1. RECUPERO DATI FORMULA
$stmt = $pdo->prepare("SELECT f.*, n.nome, n.cognome FROM formulazioni f JOIN nasi n ON f.id_naso = n.id WHERE f.id = ?");
$stmt->execute([$id_f]);
$formula = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$formula) { die("Formulazione non trovata."); }

$puo_modificare = ($_SESSION['ruolo'] === 'admin' || $_SESSION['id_naso'] == $formula['id_naso']);

// 2. LOGICA PASSAGGIO A STATO "test_in_corso"
if ($puo_modificare && isset($_GET['azione']) && $_GET['azione'] == 'termina') {
    $stmt = $pdo->prepare("UPDATE formulazioni SET stato = 'test_in_corso' WHERE id = ?");
    $stmt->execute([$id_f]);
    header("Location: dettaglio_formulazione.php?id=".$id_f); exit;
}

// 3. LOGICA AGGIUNTA/SOMMA ESSENZA (Sottrae dal magazzino)
if ($puo_modificare && $formula['stato'] == 'in_lavorazione' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aggiungi_essenza'])) {
    $id_ess = $_POST['id_essenza'];
    $qta = (float)$_POST['quantita_grammi'];
    
    $stmt_check = $pdo->prepare("SELECT quantita_g FROM essenze WHERE id = ?");
    $stmt_check->execute([$id_ess]);
    $ess = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($ess && $ess['quantita_g'] >= $qta) {
        $pdo->beginTransaction();
        try {
            // Verifica se l'essenza è già presente
            $stmt_ver = $pdo->prepare("SELECT id, quantita_grammi FROM composizione WHERE id_formulazione = ? AND id_essenza = ?");
            $stmt_ver->execute([$id_f, $id_ess]);
            $esistente = $stmt_ver->fetch(PDO::FETCH_ASSOC);
            
            // Sottrai sempre dal magazzino
            $pdo->prepare("UPDATE essenze SET quantita_g = quantita_g - ? WHERE id = ?")->execute([$qta, $id_ess]);
            
            if ($esistente) {
                // Aggiorna riga esistente
                $pdo->prepare("UPDATE composizione SET quantita_grammi = quantita_grammi + ? WHERE id = ?")->execute([$qta, $esistente['id']]);
            } else {
                // Nuova riga
                $pdo->prepare("INSERT INTO composizione (id_formulazione, id_essenza, quantita_grammi) VALUES (?, ?, ?)")->execute([$id_f, $id_ess, $qta]);
            }
            
            $pdo->commit();
            header("Location: dettaglio_formulazione.php?id=".$id_f); exit;
        } catch (Exception $e) { $pdo->rollBack(); $errore = "Errore salvataggio."; }
    } else {
        $errore = "Disponibilità insufficiente in magazzino!";
    }
}

// 4. LOGICA ELIMINAZIONE ESSENZA (Riaccredita al magazzino)
if ($puo_modificare && $formula['stato'] == 'in_lavorazione' && isset($_GET['elimina_id'])) {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT id_essenza, quantita_grammi FROM composizione WHERE id = ? AND id_formulazione = ?");
        $stmt->execute([$_GET['elimina_id'], $id_f]);
        $riga = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($riga) {
            $pdo->prepare("UPDATE essenze SET quantita_g = quantita_g + ? WHERE id = ?")->execute([$riga['quantita_grammi'], $riga['id_essenza']]);
            $pdo->prepare("DELETE FROM composizione WHERE id = ?")->execute([$_GET['elimina_id']]);
            $pdo->commit();
        }
        header("Location: dettaglio_formulazione.php?id=".$id_f); exit;
    } catch (Exception $e) { $pdo->rollBack(); }
}

$ess = $pdo->query("SELECT * FROM essenze ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$lista = $pdo->prepare("SELECT c.*, e.nome FROM composizione c JOIN essenze e ON c.id_essenza = e.id WHERE c.id_formulazione = ?");
$lista->execute([$id_f]);
$lista = $lista->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Dettaglio Formulazione</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h3><?php echo htmlspecialchars($formula['nome_profumo']); ?></h3>
        <p>Creatore: <?php echo htmlspecialchars($formula['nome'].' '.$formula['cognome']); ?> | Stato: <strong><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($formula['stato']))); ?></strong></p>

        <?php if (isset($errore)): ?><div class="alert alert-danger"><?php echo $errore; ?></div><?php endif; ?>

        <?php if ($puo_modificare && $formula['stato'] == 'in_lavorazione'): ?>
            <form method="POST" class="row g-3 my-4 bg-white p-3 shadow-sm rounded border">
                <div class="col-md-6">
                    <select name="id_essenza" class="form-control" required>
                        <?php foreach ($ess as $e): ?>
                            <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nome']); ?> (Disp: <?php echo number_format($e['quantita_g'], 2); ?>g)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3"><input type="number" step="0.001" name="quantita_grammi" placeholder="Grammi" class="form-control" required></div>
                <div class="col-md-3"><button type="submit" name="aggiungi_essenza" class="btn btn-gestione w-100">Aggiungi</button></div>
            </form>
        <?php endif; ?>

        <table class="table table-hover bg-white shadow-sm">
            <thead class="table-dark"><tr><th>Essenza</th><th>Quantità</th><th>Azioni</th></tr></thead>
            <tbody>
                <?php foreach ($lista as $i): ?>
                <tr>
                    <td><?php echo htmlspecialchars($i['nome']); ?></td>
                    <td><?php echo number_format($i['quantita_grammi'], 3); ?>g</td>
                    <td>
                        <?php if ($puo_modificare && $formula['stato'] == 'in_lavorazione'): ?>
                            <a href="?id=<?php echo $id_f; ?>&elimina_id=<?php echo $i['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Rimuovere e riaccreditare in magazzino?');">Elimina</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if ($puo_modificare && $formula['stato'] == 'in_lavorazione'): ?>
            <a href="?id=<?php echo $id_f; ?>&azione=termina" class="btn btn-success">Termina Composizione</a>
        <?php endif; ?>
    </div>
</body>
</html>