<?php
// app/Exports/PlantillaProductosSheet.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PlantillaProductosSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $comercialId;
    protected $match;

    public function __construct($comercialId, $match)
    {
        $this->comercialId = $comercialId;
        $this->match = $match;
    }

    public function headings(): array
    {
        return [
            'codigo*',
            'nombre*',
            'descripcion2',
            'descripcion3',
            'marca',
            'referencia',
            'instancia_codigo',
            'unidad',
            'costo',
            'precio_venta',
            'activo'
        ];
    }

    public function array(): array
    {
        // Datos de ejemplo
        return [
            [
                'PR001',
                'Producto de ejemplo 1',
                'Descripción adicional',
                'Detalles específicos',
                'Marca Ejemplo',
                'REF001',
                '1',
                'Unidad',
                '100.00',
                '150.00',
                'Si'
            ],
            [
                'PR002',
                'Producto de ejemplo 2',
                '',
                '',
                'Marca Ejemplo 2',
                'REF002',
                '',
                'Kg',
                '200.00',
                '300.00',
                'Si'
            ],
            [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            [
                'INSTRUCCIONES:',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            [
                '1. Los campos marcados con * son obligatorios',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            [
                '2. Para la instancia, debe usar el codigo de la misma (consulte la hoja "Instancias" para ver las disponibles) el valor que debe usar es el "codigo de instancia"',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            [
                '3. El campo "activo" acepta: Si/No, 1/0, Activo/Inactivo',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            [
                '4. Costo y precio venta: Use punto (.) como separador decimal',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
        ];
    }

    public function title(): string
    {
        return 'Productos a Importar';
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para los encabezados
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        // Autoajustar columnas
        foreach(range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Congelar primera fila
        $sheet->freezePane('A2');

        // Resaltar campos obligatorios
        $sheet->getStyle('A1:B1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ED7D31']
            ]
        ]);

        // Estilo para las instrucciones
        $sheet->getStyle('A4:L8')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2']
            ]
        ]);

        // Agregar validación de datos para la columna "activo"
        $validation = $sheet->getCell('L2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"Si,No,Activo,Inactivo,1,0"');

        // Aplicar validación a toda la columna L
        for ($i = 2; $i <= 100; $i++) {
            $sheet->getCell('L' . $i)->setDataValidation(clone $validation);
        }

        return $sheet;
    }
}
