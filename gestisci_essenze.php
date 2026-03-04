<?php
require_once 'auth.php'; 
check_permission(['admin', 'magazziniere', 'esperto']); 
require_once 'db.php';

$puo_modificare = in_array($_SESSION['ruolo'], ['admin', 'magazziniere']);
$puo_eliminare = in_array($_SESSION['ruolo'], ['admin', 'magazziniere']);

// 1. LOGICA INSERIMENTO O SOMMA
if ($puo_modificare && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aggiungi_essenza'])) {
    $nome = $_POST['nome'];
    $famiglia = $_POST['famiglia'];
    $quantita = (float)$_POST['quantita_g'];

    $stmt = $pdo->prepare("SELECT id, quantita_g FROM essenze WHERE nome = ? AND famiglia = ?");
    $stmt->execute([$nome, $famiglia]);
    $esistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($esistente) {
        $nuova_quantita = $esistente['quantita_g'] + $quantita;
        $stmt = $pdo->prepare("UPDATE essenze SET quantita_g = ? WHERE id = ?");
        $stmt->execute([$nuova_quantita, $esistente['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO essenze (nome, famiglia, quantita_g) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $famiglia, $quantita]);
    }
    header("Location: gestisci_essenze.php");
    exit;
}

// 2. LOGICA AGGIORNAMENTO
if ($puo_modificare && isset($_GET['update_id']) && isset($_GET['new_qty'])) {
    $stmt = $pdo->prepare("UPDATE essenze SET quantita_g = ? WHERE id = ?");
    $stmt->execute([(float)$_GET['new_qty'], $_GET['update_id']]);
    header("Location: gestisci_essenze.php");
    exit;
}

// 3. LOGICA ELIMINAZIONE (Admin e Magazziniere)
if ($puo_eliminare && isset($_GET['elimina_id'])) {
    $stmt = $pdo->prepare("DELETE FROM essenze WHERE id = ?");
    $stmt->execute([$_GET['elimina_id']]);
    header("Location: gestisci_essenze.php");
    exit;
}

$essenze = $pdo->query("SELECT * FROM essenze ORDER BY famiglia, nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Magazzino Essenze</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h3>Magazzino Essenze</h3>
        
        <?php if ($puo_modificare): ?>
            <form method="POST" class="row g-3 my-4 bg-white p-3 shadow-sm rounded border">
                <div class="col-md-4"><input type="text" name="nome" placeholder="Nome Essenza" class="form-control" required></div>
                <div class="col-md-3">
                    <select name="famiglia" class="form-control">
                        <option value="fiorita">Fiorita</option>
                        <option value="legnosa">Legnosa</option>
                        <option value="orientale">Orientale</option>
                        <option value="fresca">Fresca</option>
                    </select>
                </div>
                <div class="col-md-3"><input type="number" step="0.001" name="quantita_g" placeholder="Giacenza (g)" class="form-control" required></div>
                <div class="col-md-2"><button type="submit" name="aggiungi_essenza" class="btn btn-gestione w-100">Aggiungi</button></div>
            </form>
        <?php endif; ?>

        <table class="table table-hover mt-3 bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Famiglia</th>
                    <th>Giacenza (g)</th>
                    <?php if ($puo_eliminare): ?><th>Azioni</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($essenze as $e): 
                    $fam = strtolower($e['famiglia']);
                    $badge_famiglia = 'badge-secondary';
                    if ($fam == 'fiorita') $badge_famiglia = 'badge-fiorita';
                    elseif ($fam == 'legnosa') $badge_famiglia = 'badge-legnosa';
                    elseif ($fam == 'orientale') $badge_famiglia = 'badge-orientale';
                    elseif ($fam == 'fresca') $badge_famiglia = 'badge-fresca';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($e['nome']); ?></td>
                    <td><span class="badge <?php echo $badge_famiglia; ?>"><?php echo ucfirst($e['famiglia']); ?></span></td>
                    <td>
                        <?php if ($puo_modificare): ?>
                            <form action="" method="GET" class="d-flex align-items-center">
                                <input type="hidden" name="update_id" value="<?php echo $e['id']; ?>">
                                <input type="number" step="0.001" name="new_qty" value="<?php echo number_format((float)$e['quantita_g'], 3, '.', ''); ?>" class="form-control form-control-sm qty-input" style="width: 100px;" disabled>
                                <button type="button" class="btn btn-sm btn-modifica ms-2 btn-edit">Modifica</button>
                                <button type="submit" class="btn btn-sm btn-ok ms-2 btn-save">Ok</button>
                            </form>
                        <?php else: ?>
                            <?php echo number_format((float)$e['quantita_g'], 3, '.', ''); ?> g
                        <?php endif; ?>
                    </td>
                    <?php if ($puo_eliminare): ?>
                    <td>
                        <a href="?elimina_id=<?php echo $e['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Sei sicuro di voler eliminare questa essenza?');">
                           Elimina
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('form');
                row.querySelector('.qty-input').disabled = false;
                this.style.display = 'none';
                row.querySelector('.btn-save').style.display = 'inline-block';
            });
        });
    </script>
</body>
</html>