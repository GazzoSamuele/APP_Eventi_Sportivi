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
        // i nomi degli header non distinguono maiuscole e minuscole (Dart li manda minuscoli)
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        $authorization = $headers['authorization'] ?? '';

        if (!str_starts_with($authorization, 'Bearer ')) {
            return null;
        }

        return substr($authorization, 7);
    }
}
