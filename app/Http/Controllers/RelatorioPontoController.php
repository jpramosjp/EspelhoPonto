<?php

namespace App\Http\Controllers;

use App\Exports\ExportarExcel;
use App\Service\EstruturaArquivo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class RelatorioPontoController extends Controller
{

    public function tela() {
        return view("tela_relatorio");
    }



    public function gerarRelatorio(Request $request) {

        $request->validate([
            'file' => 'required|file|mimes:txt,pdf,docx|max:2048',
        ], [
            'file.required' => 'O arquivo é obrigatório.',
            'file.file' => 'O arquivo deve ser um arquivo.',
            'file.mimes' => 'O arquivo deve ser um dos seguintes tipos: txt',
            'file.max' => 'O arquivo não pode ser maior que 2 MB.',
        ]);
        $arquivoRequisicao = $request->file('file');
        $arquivo = file($arquivoRequisicao);

        unset($arquivo[0]);
        $listaFuncionarios = Storage::json('de_para.json');
        foreach($arquivo as $key => $valor) {

            $tipoLinha = substr($valor, 9, 1);
            if($tipoLinha != 3) {
                continue;
            }

            $dadosTratados = unpack(EstruturaArquivo::estruturaTipo3(), $valor);



            if(!empty($listaFuncionarios["funcionarios"][$dadosTratados["funcionario"]])){
                $dadosTratados["funcionario"] = $listaFuncionarios["funcionarios"][$dadosTratados["funcionario"]];
            }

            
            $dataHora = Carbon::parse($dadosTratados["data_hora"]);

            $data = $dataHora->format("d/m/Y"); 
            $hora = $dataHora->format("H:i:s"); 
            
            $dadosRelatorios[$dadosTratados["funcionario"]][$data][] = $hora;
        }
        return Excel::download(new ExportarExcel($dadosRelatorios), 'Relatorio de Pontos ' . Carbon::now()->format("d_m_Y") . '.xlsx');
    }
}
