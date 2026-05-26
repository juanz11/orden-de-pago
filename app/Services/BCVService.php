<?php

namespace App\Services;

class BCVService
{
    /**
     * Obtiene la tasa actual del BCV
     * Por ahora retorna un valor fijo, pero aquí se podría implementar
     * la lógica para obtener la tasa real del BCV via API o web scraping
     */
    public static function getCurrentRate()
    {
        return 91.92; // Tasa actual
    }
}
