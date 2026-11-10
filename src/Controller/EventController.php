<?php

namespace Samuele\AppCalendarioEventiSportivi\Controller;

use PDO;
use Samuele\AppCalendarioEventiSportivi\Auth;
use Samuele\AppCalendarioEventiSportivi\Http\Response;
use Samuele\AppCalendarioEventiSportivi\Http\Request;

class EventController
{
    public function __construct(private PDO $pdo, private Auth $auth, private string $appUrl) {}

    public function index(): void
    {
        $utente = $this->auth->richiediUtente();

        $righe = $this->eventiUtente($utente['id']);

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

    public function feedToken(): void
    {
        $utente = $this->auth->richiediUtente();

        $token = bin2hex(random_bytes(32));

        $stmt = $this->pdo->prepare('UPDATE users SET feed_token = :token WHERE id = :id');

        $stmt->execute([
            ':token' => $token,
            ':id' => $utente['id'],
        ]);

        Response::json(['url' => $this->appUrl . '/api/calendar.ics?token=' . $token]);
    }

    private function eventiUtente(int $userId): array
    {
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
            ':user_id' => $userId,
        ]);

        return $stmt->fetchAll();
    }

    public function calendar(): void
    {
        $token = trim($_GET['token'] ?? '');

        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE feed_token = :token');
        $stmt->execute([
            ':token' => $token,
        ]);

        $utente = $stmt->fetch();

        if (!$utente) {
            Response::errore('Calendario non trovato', 404);
            return;
        }

        $righe = $this->eventiUtente($utente['id']);

        // le righe del file, una per elemento
        $ics = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Calendario Sportivo//IT',
            'X-WR-CALNAME:I miei eventi sportivi',
        ];

        foreach ($righe as $riga) {
            // le gare non hanno una fine: diamo per buone 2 ore
            $fine = $riga['end_utc'] ?? gmdate('Y-m-d H:i:s', strtotime($riga['start_utc'] . ' UTC') + 2 * 3600);

            $ics[] = 'BEGIN:VEVENT';
            $ics[] = 'UID:evento-' . $riga['id'] . '@calendario-sportivo';
            $ics[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
            $ics[] = 'DTSTART:' . $this->dataIcs($riga['start_utc']);
            $ics[] = 'DTEND:' . $this->dataIcs($fine);
            $ics[] = 'SUMMARY:' . $this->testoIcs($riga['competition'] . ' - ' . $riga['title']);
            $ics[] = 'LOCATION:' . $this->testoIcs($riga['location'] ?? '');
            $ics[] = 'DESCRIPTION:' . $this->testoIcs('Dove vederlo: ' . ($riga['broadcasters'] ?? 'non disponibile'));
            $ics[] = 'END:VEVENT';
        }

        $ics[] = 'END:VCALENDAR';

        header('Content-Type: text/calendar; charset=utf-8');
        echo implode("\r\n", $ics);
    }

    // "2027-03-14 04:00:00" → "20270314T040000Z"
    private function dataIcs(string $data): string
    {
        return str_replace(['-', ':', ' '], ['', '', 'T'], $data) . 'Z';
    }

    // protegge i caratteri speciali del formato .ics
    private function testoIcs(string $testo): string
    {
        return str_replace(['\\', ',', ';'], ['\\\\', '\,', '\;'], $testo);
    }
}
