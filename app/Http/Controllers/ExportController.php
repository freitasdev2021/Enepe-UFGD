<?php

namespace App\Http\Controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DB;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportRespostas($id)
    {
        // Consulta os registros
        $registros = DB::select("
            SELECT r.Respostas, r.id, u.name 
            FROM respostas r 
            INNER JOIN formularios f ON (f.id = r.IDForm) 
            INNER JOIN users u ON (r.IDUser = u.id) 
            WHERE f.id = :id", ['id' => $id]);

        // Inicializa a planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define o cabeçalho
        $sheet->setCellValue('A1', 'Nome');
        $colIndex = 'B';

        if (count($registros) > 0) {
            $primeiroRegistro = json_decode($registros[0]->Respostas, true);
            foreach ($primeiroRegistro as $r) {
                $sheet->setCellValue($colIndex . '1', $r['Conteudo']);
                $colIndex++;
            }
        }

        // Preenche os dados
        $row = 2; // Começa na segunda linha (a primeira é o cabeçalho)
        foreach ($registros as $registro) {
            $item = [];
            $item[] = $registro->name;
            $respostas = json_decode($registro->Respostas, true);
            foreach ($respostas as $resposta) {
                $item[] = $resposta['Resposta'];
            }

            $colIndex = 'A';
            foreach ($item as $valor) {
                $sheet->setCellValue($colIndex . $row, $valor);
                $colIndex++;
            }
            $row++;
        }

        // Gera o arquivo Excel
        $fileName = 'resultado_' . date('Y-m-d_H-i-s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Cria a resposta para download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
