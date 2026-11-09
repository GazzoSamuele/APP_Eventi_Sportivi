<?php

namespace Samuele\AppCalendarioEventiSportivi;

use PDO;

class Database
{
    public static function connect(array $config): PDO
    {
        // risultato: mysql:host=127.0.0.1;port=3306;dbname=nome_db;charset=utf8mb4
        $dsn = 'mysql:host=' . $config['DB_HOST']
            . ';port=' . $config['DB_PORT']
            . ';dbname=' . $config['DB_DATABASE']
            . ';charset=utf8mb4';

        $options = [

            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        return new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD'], $options);
    }
}
