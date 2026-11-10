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

    public static function bearerToken(): ?string
    {
        $headers = getallheaders();
        $authorization = $headers['Authorization'] ?? '';

        if (!str_starts_with($authorization, 'Bearer ')) {
            return null;
        }

        return substr($authorization, 7);
    }
}
