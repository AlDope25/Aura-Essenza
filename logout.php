<?php
session_start();
session_destroy(); // Distrugge tutte le variabili di sessione
header("Location: login.php"); // Rimanda al login
exit;
?>