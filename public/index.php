<?php

require __DIR__ . '/../vendor/autoload.php';

use Samuele\AppCalendarioEventiSportivi\Database;
use Samuele\AppCalendarioEventiSportivi\Http\Response;
use Samuele\AppCalendarioEventiSportivi\Http\Router;
use Samuele\AppCalendarioEventiSportivi\Controller\AuthController;
use Samuele\AppCalendarioEventiSportivi\Controller\SportController;
use Samuele\AppCalendarioEventiSportivi\Controller\PreferenceController;
use Samuele\AppCalendarioEventiSportivi\Auth;

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    Response::errore('Errore interno del server', 500);
});

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// il browser manda una richiesta OPTIONS di prova prima di quelle vere: rispondiamo "ok" e basta
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// legge il .env e restituisce un array ['DB_HOST' => '127.0.0.1', ...]
$config = parse_ini_file(__DIR__ . '/../.env');

if ($config === false) {
    throw new RuntimeException('File .env mancante o non leggibile');
}

$pdo = Database::connect($config);

$router = new Router();

$auth = new AuthController($pdo);

$router->post('/api/register', fn() => $auth->register());
$router->post('/api/login', fn() => $auth->login());

$authUtente = new Auth($pdo);

$router->get('/api/me', fn() => Response::json($authUtente->richiediUtente()));

$router->post('/api/logout', fn() => $auth->logout());

$sport = new SportController($pdo);
$router->get('/api/sport', fn() => $sport->index());

$preferenze = new PreferenceController($pdo, $authUtente);
$router->get('/api/preferences', fn() => $preferenze->index());

$router->get('/api/ping', function () {
    Response::json(['ok' => true]);
});

$router->get('/api/db', function () use ($pdo) {
    $pdo->query('SELECT 1');
    Response::json(['database' => 'ok']);
});

// percorso senza la parte ?chiave=valore (es. /api/eventi?mese=11 → /api/eventi)
$percorso = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($_SERVER['REQUEST_METHOD'], $percorso);
