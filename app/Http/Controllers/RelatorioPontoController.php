<?php

namespace App\Http\Controllers;

use App\Exports\ExportarExcel;
use App\Service\EstruturaArquivo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
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

        // Carregar lista de funcionários do JSON
        $listaFuncionarios = Storage::json('de_para.json');

        // Descriptografar CPFs no JSON para facilitar a busca
        $funcionariosDescriptografados = [];
        foreach ($listaFuncionarios["funcionarios"] as $cpfCriptografado => $nome) {
            try {
                $cpf = Crypt::decrypt($cpfCriptografado);
                $funcionariosDescriptografados[$cpf] = $nome;
            } catch (\Exception $e) {
                Log::error("Erro ao descriptografar CPF: " . $e->getMessage());
            }
        }

        foreach ($arquivo as $key => $valor) {

            $tipoLinha = substr($valor, 9, 1);
            if ($tipoLinha != 3) {
                continue;
            }

            $dadosTratados = unpack(EstruturaArquivo::estruturaTipo3(), $valor);

            // Buscar o nome do funcionário na lista descriptografada
            if (!empty($funcionariosDescriptografados[$dadosTratados["funcionario"]])) {
                $dadosTratados["funcionario"] = $funcionariosDescriptografados[$dadosTratados["funcionario"]];
            }

            $dataHora = Carbon::parse($dadosTratados["data_hora"]);
            $data = $dataHora->format("d/m/Y"); 
            $hora = $dataHora->format("H:i:s"); 
            
            $dadosRelatorios[$dadosTratados["funcionario"]][$data][] = $hora;
        }

        return Excel::download(new ExportarExcel($dadosRelatorios), 'Relatorio de Pontos ' . Carbon::now()->format("d_m_Y") . '.xlsx');
    }
}
