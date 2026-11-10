<?php

namespace Samuele\AppCalendarioEventiSportivi\Controller;

use PDO;
use Samuele\AppCalendarioEventiSportivi\Auth;
use Samuele\AppCalendarioEventiSportivi\Http\Response;
use Samuele\AppCalendarioEventiSportivi\Http\Request;

class EventController
{
    public function __construct(private PDO $pdo, private Auth $auth) {}

    public function index(): void
    {
        $utente = $this->auth->richiediUtente();
        $stmt = $this->pdo->prepare("SELECT e.id, e.title, e.location, e.start_utc, e.end_utc, e.status,
            s.name AS sport,
            co.name AS competition,
            GROUP_CONCAT(b.name ORDER BY b.name SEPARATOR ', ') AS broadcasters
        FROM events e
        JOIN user_competitions uc ON uc.competition_id = e.competition_id
                                AND uc.user_id = :user_id
        JOIN competitions co ON co.id = e.competition_id
        JOIN categories c ON c.id = co.category_id
        JOIN sport s ON s.id = c.sport_id
        LEFT JOIN (
            SELECT eb.event_id, eb.broadcaster_id
            FROM event_broadcaster eb
            UNION
            SELECT e2.id, cb.broadcaster_id
            FROM events e2
            JOIN competition_broadcaster cb ON cb.competition_id = e2.competition_id
            WHERE NOT EXISTS (SELECT 1 FROM event_broadcaster x WHERE x.event_id = e2.id)
        ) AS dv ON dv.event_id = e.id
        LEFT JOIN broadcasters b ON b.id = dv.broadcaster_id
        WHERE e.start_utc >= UTC_TIMESTAMP()
        GROUP BY e.id
        ORDER BY e.start_utc");

        $stmt->execute([
            ':user_id' => $utente['id'],
        ]);

        $righe = $stmt->fetchAll();

        $eventi = [];

        foreach ($righe as $riga) {
            $riga['start_utc'] = str_replace(' ', 'T', $riga['start_utc']) . 'Z';

            if ($riga['end_utc'] !== null) {
                $riga['end_utc'] = str_replace(' ', 'T', $riga['end_utc']) . 'Z';
            }

            if ($riga['broadcasters'] === null) {
                $riga['broadcasters'] = [];
            } else {
                $riga['broadcasters'] = explode(', ', $riga['broadcasters']);
            }

            $eventi[] = $riga;
        }

        Response::json($eventi);
    }
}
