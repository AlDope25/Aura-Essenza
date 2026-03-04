# 🌿 Aura & Essenza - Management System per Profumeria Artigianale

**Sviluppatore:** Aldo Petrone  
**Versione:** 1.5 (Final)

## 📋 Descrizione del Progetto
Aura & Essenza è una web app professionale progettata per ottimizzare la gestione operativa di un laboratorio di profumeria artigianale. Il sistema permette il monitoraggio centralizzato delle materie prime (essenze) e la tracciabilità granulare delle formulazioni create dai "Nasi" esperti.

Il software garantisce:
- **Gestione Gerarchica**: Ruoli differenziati per Admin, Esperti, Magazzinieri e Controllo Qualità (RBAC).
- **Tracciabilità**: Monitoraggio quotidiano delle fasi di produzione e approvazione.
- **Sicurezza**: Password protette tramite algoritmi di hashing e transazioni SQL sicure.

## 📂 Struttura del Repository
Il progetto è organizzato per garantire la massima manutenibilità e chiarezza:

- `📁 documentazione/`: Contiene il Documento di Progettazione (SRS), i diagrammi E-R, il diagramma delle classi e il dump SQL per la creazione del database.
- `📁 tests/`: Suite di test automatici eseguiti con PHPUnit per la validazione della connettività e dell'autenticazione.
- `📄 file .php`: Logica applicativa e interfaccia utente della web app.

## 🛠️ Stack Tecnologico
- **Linguaggio**: PHP 8.2.12
- **Database**: MySQL tramite interfaccia PDO
- **Testing Framework**: PHPUnit 10.5.63
- **Ambiente**: Stack LAMP/XAMPP

## 🧪 Esecuzione dei Test
Per verificare l'integrità del sistema e della connessione al database, posizionarsi nella root del progetto ed eseguire:

```bash
# Test di connettività al Database
D:\xampp\php\php.exe vendor\bin\phpunit tests/DatabaseTest.php

# Test del sistema di Autenticazione (Login)
D:\xampp\php\php.exe vendor\bin\phpunit tests/LoginTest.php
