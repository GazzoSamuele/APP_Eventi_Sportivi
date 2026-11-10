<?php

namespace Samuele\AppCalendarioEventiSportivi\Controller;

use PDO;
use Samuele\AppCalendarioEventiSportivi\Auth;
use Samuele\AppCalendarioEventiSportivi\Http\Response;
use Samuele\AppCalendarioEventiSportivi\Http\Request;

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
    public function update(): void
    {
        $utente = $this->auth->richiediUtente();

        $dati = Request::body();
        $ids = $dati['competition_ids'] ?? null;

        if (!is_array($ids)) {
            Response::errore('competition_ids deve essere una lista', 422);
            return;
        };

        foreach ($ids as $id) {
            if (!is_int($id)) {
                Response::errore('Ogni id deve essere un numero intero', 422);
                return;
            };
        }

        $ids = array_values(array_unique($ids));

        $esistenti = $this->pdo->query('SELECT id FROM competitions')->fetchAll(PDO::FETCH_COLUMN);

        if (array_diff($ids, $esistenti)) {
            Response::errore('Una o più competizioni non esistono', 422);
            return;
        };

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare('DELETE FROM user_competitions WHERE user_id = :user_id');
            $stmt->execute(['user_id' => $utente['id']]);

            $stmt = $this->pdo->prepare('INSERT INTO user_competitions (user_id, competition_id) VALUES (:user_id, :competition_id)');

            foreach ($ids as $id) {
                $stmt->execute([
                    'user_id' => $utente['id'],
                    'competition_id' => $id,
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        Response::json($ids);

        // 1. prendo l'utente loggato
        // 2. leggo il body e prendo competition_ids
        // 3. se non è un array → 422
        // 4. se dentro c'è qualcosa che non è un numero intero → 422
        // 5. tolgo i doppioni
        // 6. se qualche id non esiste tra le competizioni → 422
        // 7. transazione: cancello le vecchie preferenze dell'utente, inserisco le nuove
        // 8. rispondo con la lista salvata
    }
}
