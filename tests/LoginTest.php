<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {
    
    // Includiamo il database e prepariamo la connessione
    private $pdo;

    protected function setUp(): void {
        // Carichiamo la connessione dal tuo file esistente
        require __DIR__ . '/../db.php';
        $this->pdo = $pdo; // $pdo viene dal tuo db.php
    }

    // Funzione di utilità per simulare la logica di login
    private function autenticaUtente($pdo, $id, $password) {
        $stmt = $pdo->prepare("SELECT password FROM utenti WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $utente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utente && password_verify($password, $utente['password'])) {
            return true;
        }
        return false;
    }

    // 1. Test: Login Admin corretto (ID 1, password aura2026)
    public function testLoginAdminSuccesso() {
        $risultato = $this->autenticaUtente($this->pdo, 1, 'aura2026');
        $this->assertTrue($risultato, "Il login con password corretta deve avere successo");
    }

    // 2. Test: Password errata
    public function testLoginPasswordErrata() {
        $risultato = $this->autenticaUtente($this->pdo, 1, 'sbagliata');
        $this->assertFalse($risultato, "Il login deve fallire con password errata");
    }

    // 3. Test: ID non esistente
    public function testLoginIdInesistente() {
        $risultato = $this->autenticaUtente($this->pdo, 99999, 'aura2026');
        $this->assertFalse($risultato, "Il login deve fallire se l'ID non esiste");
    }
}