<?php

namespace Samuele\AppCalendarioEventiSportivi\Http;

class Response
{
    public static function json(mixed $dati, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($dati, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    public static function errore(string $messaggio, int $status): void
    {
        self::json(['errore' => $messaggio], $status);
    }
}
