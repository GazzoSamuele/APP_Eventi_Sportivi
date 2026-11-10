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
}
