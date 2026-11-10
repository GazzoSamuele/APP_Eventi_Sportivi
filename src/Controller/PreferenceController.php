<?php

namespace Samuele\AppCalendarioEventiSportivi\Controller;

use PDO;
use Samuele\AppCalendarioEventiSportivi\Auth;
use Samuele\AppCalendarioEventiSportivi\Http\Response;

class PreferenceController
{
    public function __construct(private PDO $pdo, private Auth $auth) {}

    public function index(): void
    {
        $utente = $this->auth->richiediUtente();

        $stmt = $this->pdo->prepare('SELECT competition_id FROM user_competitions WHERE user_id = :user_id');
        $stmt->execute([
            ':user_id' => $utente['id'],
        ]);

        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        Response::json($ids);
        // 1. prendo l'utente loggato (se non c'è il token → 401, ci pensa richiediUtente)
        // 2. cerco nel database le competition_id di quell'utente in user_competitions
        // 3. rispondo con la lista degli id
    }
}
