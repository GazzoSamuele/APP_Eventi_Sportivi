<?php

namespace Samuele\AppCalendarioEventiSportivi\Controller;

use PDO;
use Samuele\AppCalendarioEventiSportivi\Http\Request;
use Samuele\AppCalendarioEventiSportivi\Http\Response;

class AuthController
{
    public function __construct(private PDO $pdo) {}

    public function register(): void
    {
        $dati = Request::body();

        $email = strtolower(trim($dati['email'] ?? ''));
        $password = $dati['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::errore('Email non valida', 422);
            return;
        }

        if (strlen($password) < 8) {
            Response::errore('La password deve avere almeno 8 caratteri', 422);
            return;
        }


        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $utente = $stmt->fetch();

        if ($utente) {
            Response::errore('Email già registrata', 409);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare('INSERT INTO users (email, password_hash) VALUES (:email, :hash)');
        $stmt->execute([
            'email' => $email,
            'hash' => $hash
        ]);
        Response::json([
            'id' => (int) $this->pdo->lastInsertId(),
            'email' => $email
        ], 201);
    }

    public function login(): void
    {
        $dati = Request::body();

        $email = strtolower(trim($dati['email'] ?? ''));
        $password = $dati['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::errore('Email non valida', 422);
            return;
        }


        $stmt = $this->pdo->prepare('SELECT id, password_hash FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $utente = $stmt->fetch();

        if (!$utente || !password_verify($password, $utente['password_hash'])) {
            Response::errore('Credenziali non valide', 401);
            return;
        }

        $token = bin2hex(random_bytes(32));

        $stmt = $this->pdo->prepare('INSERT INTO access_tokens (user_id, token_hash, expires_at) VALUES (:user_id, :hash, UTC_TIMESTAMP() + INTERVAL 30 DAY)');
        $stmt->execute([
            'user_id' => $utente['id'],
            'hash' => hash('sha256', $token)
        ]);

        Response::json([
            'token' => $token
        ]);
    }

    public function logout(): void
    {
        $token = Request::bearerToken();

        if ($token === null) {
            Response::errore('Non autenticato', 401);
            return;
        }

        $stmt = $this->pdo->prepare('DELETE FROM access_tokens WHERE token_hash = :hash');
        $stmt->execute([
            ':hash' => hash('sha256', $token),
        ]);

        if ($stmt->rowCount() === 0) {
            Response::errore('Non autenticato', 401);
            return;
        }

        Response::json(['ok' => true]);
    }
}
