<?php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    public function testConnessioneDatabase() {
        require __DIR__ . '/../db.php';
        
        // Verifichiamo direttamente l'istanza senza usare la variabile nell'errore
        $this->assertInstanceOf(PDO::class, $pdo, "La connessione non è un oggetto PDO valido.");
    }
}