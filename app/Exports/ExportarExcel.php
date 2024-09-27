<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExportarExcel implements WithMultipleSheets
{
    use Exportable;

    protected $dados;

    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->dados as $nome => $dadosNome) {
            $sheets[] = new DadosSheet($nome, $dadosNome);
        }

        return $sheets;
    }
}

class DadosSheet implements FromArray, WithTitle, WithStyles
{
    protected $nome;
    protected $dados;

    public function __construct($nome, $dados)
    {
        $this->nome = $nome;
        $this->dados = $dados;
    }

    public function array(): array
    {
        $array = [
            ['DATA', 'ENTRADA 1', 'SAÍDA 1', 'ENTRADA 2', 'SAÍDA 2']
        ];

        foreach ($this->dados as $data => $pontos) {
            $array[] = array_merge([$data], $pontos);
        }

        return $array;
    }

    public function title(): string
    {
        return $this->nome;
    }

    public function styles(Worksheet $sheet)
    {
        // Aplica negrito à primeira coluna (DATA)
        return [
            'A1:A' . $sheet->getHighestRow() => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}
