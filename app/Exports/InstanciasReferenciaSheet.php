<?php
// app/Exports/InstanciasReferenciaSheet.php

namespace App\Exports;

use App\Models\Sainsta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InstanciasReferenciaSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $match;

    public function __construct($match)
    {
        $this->match = $match;
    }

    public function collection()
    {
        $instancias = Sainsta::where('comercial', $this->match)
            ->orderBy('codalte', 'asc')
            ->get();

        $data = [];
        foreach ($instancias as $instancia) {
            $nivel = $instancia->nivel;
            $indentacion = str_repeat('  ', ($nivel - 1) * 2);

            $data[] = [
                'codigo' => $instancia->codinst,
                'nombre' => $indentacion . $instancia->descrip,
                'nombre_sin_formato' => $instancia->descrip,
                'nivel' => $instancia->nivel,
                'codalte' => $instancia->codalte,
                'cantidad_productos' => $instancia->productos->count()
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Código de Instancia',
            'Nombre (con jerarquía)',
            'Nombre (sin formato)',
            'Nivel',
            'Código Alterno',
            'Cantidad de Productos'
        ];
    }

    public function title(): string
    {
        return 'Instancias Disponibles';
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para los encabezados
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '70AD47']
            ]
        ]);

        // Autoajustar columnas
        foreach(range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Congelar primera fila
        $sheet->freezePane('A2');

        // Aplicar filtros
        $sheet->setAutoFilter('A1:F1');

        return $sheet;
    }
}
