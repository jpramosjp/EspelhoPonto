<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;

class EstruturaArquivo {
    

    public static function estruturaTipo3 () {
        return "A9numero_sequencial/" .
                "A1tipo_linha" .
                "/A24data_hora" .
                "/A12funcionario" .
                "/A4crc";
    }
}
