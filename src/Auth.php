<?php

namespace Samuele\AppCalendarioEventiSportivi;

use PDO;
use Samuele\AppCalendarioEventiSportivi\Http\Request;
use Samuele\AppCalendarioEventiSportivi\Http\Response;

class Auth
{
    public function __construct(private PDO $pdo) {}

    public function utente(): ?array
    {
        $token = Request::bearerToken();

        if ($token === null) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT u.id, u.email, u.timezone FROM access_tokens t JOIN users u ON u.id = t.user_id WHERE t.token_hash = :hash AND t.expires_at > UTC_TIMESTAMP()');
        $stmt->execute([
            ':hash' => hash('sha256', $token),
        ]);

        return $stmt->fetch() ?: null;
    }

    public function richiediUtente(): array
    {
        $utente = $this->utente();

        if ($utente === null) {
            Response::errore('Non autenticato', 401);
            exit;
        }

        return $utente;
    }
}
