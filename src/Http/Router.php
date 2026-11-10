<?php

namespace Samuele\AppCalendarioEventiSportivi\Http;

class Router
{
    private array $rotte = [];

    public function get(string $percorso, callable $azione): void
    {
        $this->rotte['GET'][$percorso] = $azione;
    }

    public function post(string $percorso, callable $azione): void
    {
        $this->rotte['POST'][$percorso] = $azione;
    }

    public function put(string $percorso, callable $azione): void
    {
        $this->rotte['PUT'][$percorso] = $azione;
    }

    public function dispatch(string $metodo, string $percorso): void
    {
        // 1. la rotta esiste per questo metodo: prendo la funzione salvata e la eseguo
        if (isset($this->rotte[$metodo][$percorso])) {
            $azione = $this->rotte[$metodo][$percorso];
            $azione();
            return;
        }

        // 2. il percorso esiste ma con un altro metodo 
        foreach ($this->rotte as $percorsi) {
            if (isset($percorsi[$percorso])) {
                Response::errore('Metodo non consentito', 405);
                return;
            }
        }

        // 3. il percorso non esiste proprio
        Response::errore('Rotta non trovata', 404);
    }
}
