<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="logo.png" alt="Aura & Essenza" class="navbar-logo">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="elenco_formulazioni.php">Formulazioni</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestisci_essenze.php">Magazzino Essenze</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestisci_nasi.php">Team Nasi</a>
                </li>
                
                <li class="nav-item ms-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="btn btn-outline-danger btn-sm" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="btn btn-outline-light btn-sm" href="login.php">Login Personale</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>