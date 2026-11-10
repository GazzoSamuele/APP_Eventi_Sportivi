<?php

namespace Samuele\AppCalendarioEventiSportivi\Http;

class Request
{
    public static function body(): array
    {
        $stringa = file_get_contents('php://input');
        $dati = json_decode($stringa, true);

        if (!is_array($dati)) {
            Response::errore('JSON non valido', 400);
            exit;
        }

        return $dati;
    }
}
