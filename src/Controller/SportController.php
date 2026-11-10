<?php

namespace Samuele\AppCalendarioEventiSportivi\Controller;

use PDO;
use Samuele\AppCalendarioEventiSportivi\Http\Response;

class SportController
{
    public function __construct(private PDO $pdo) {}

    public function index(): void
    {
        $righe = $this->pdo->query('SELECT s.id AS sport_id, s.name AS sport_name,
           c.id AS category_id, c.name AS category_name,
           co.id AS competition_id, co.name AS competition_name
        FROM competitions co
        JOIN categories c ON c.id = co.category_id
        JOIN sport s ON s.id = c.sport_id
        ORDER BY s.name, c.name, co.name')->fetchAll();

        $albero = [];

        foreach ($righe as $riga) {
            $sportId = $riga['sport_id'];
            $categoriaId = $riga['category_id'];

            if (!isset($albero[$sportId])) {
                $albero[$sportId] = [
                    'id' => $sportId,
                    'name' => $riga['sport_name'],
                    'categories' => [],
                ];
            }

            if (!isset($albero[$sportId]['categories'][$categoriaId])) {
                $albero[$sportId]['categories'][$categoriaId] = [
                    'id' => $categoriaId,
                    'name' => $riga['category_name'],
                    'competitions' => [],
                ];
            }

            $albero[$sportId]['categories'][$categoriaId]['competitions'][] = [
                'id' => $riga['competition_id'],
                'name' => $riga['competition_name'],
            ];
        }
        $risposta = [];

        foreach ($albero as $sport) {
            $sport['categories'] = array_values($sport['categories']);
            $risposta[] = $sport;
        }

        Response::json($risposta);
    }
}
