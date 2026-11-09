<?php

require __DIR__ . '/../vendor/autoload.php';

use Samuele\AppCalendarioEventiSportivi\Database;

// legge il .env e restituisce un array ['DB_HOST' => '127.0.0.1', ...]
$config = parse_ini_file(__DIR__ . '/../.env');

if ($config === false) {
    http_response_code(500);
    exit('Configurazione errata: file .env mancante');
}

try {
    $pdo = Database::connect($config);
} catch (PDOException $e) {

    // il messaggio vero va nel log, mai a chi visita la pagina
    error_log('DB non raggiungibile: ' . $e->getMessage());
    http_response_code(500);
    exit('Servizio momentaneamente non disponibile');
}

// query di prova: verrà sostituita dal router nella Fase 1
$righe = $pdo->query('SELECT * FROM prova')->fetchAll();

echo '<pre>';
print_r($righe);
echo '</pre>';
