<?php
// app/Exports/InstruccionesSheet.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InstruccionesSheet implements FromArray, WithTitle, WithStyles
{
    public function array(): array
    {
        return [
            ['INSTRUCCIONES PARA IMPORTAR PRODUCTOS'],
            [''],
            ['1. PREPARACIÓN DEL ARCHIVO:'],
            ['   - Complete los datos en la hoja "Productos a Importar"'],
            ['   - Los campos marcados con * son obligatorios'],
            ['   - No modifique los nombres de las columnas'],
            ['   - Puede agregar tantas filas como productos necesite'],
            [''],
            ['2. CÓMO IDENTIFICAR LA INSTANCIA:'],
            ['   - Debe usar el "Código de Instancia"  '],
            ['   - Consulte la hoja "Instancias Disponibles" para ver las opciones válidas'],
            ['   - Ejemplo de jerarquía: Electrónica > Computadoras > Laptops'],
            [''],
            ['3. FORMATOS DE DATOS:'],
            ['   - Código: Texto, máximo 15 caracteres'],
            ['   - Nombre: Texto descriptivo del producto'],
            ['   - Costo y Precio: Números decimales usando punto (.) Ej: 100.50'],
            ['   - Activo: Valores válidos: Si, No, 1, 0, Activo, Inactivo'],
            [''],
            ['4. VALIDACIONES:'],
            ['   - El código del producto debe ser único en el sistema'],
            ['   - La instancia debe existir en el sistema (ver hoja de instancias)'],
            ['   - Los productos se crearán automáticamente para todas las empresas del grupo'],
            [''],
            ['5. DESPUÉS DE LA IMPORTACIÓN:'],
            ['   - Se mostrará un resumen de productos creados'],
            ['   - Los errores se listarán para su corrección'],
            ['   - Los productos exitosos estarán disponibles inmediatamente'],
            [''],
            ['EJEMPLO DE DATOS VÁLIDOS:'],
            ['   Código: PR-001-ABC'],
            ['   Nombre: Laptop Dell Inspiron 15'],
            ['   Instancia: 5 (código de instancia)'],
            ['   Costo: 850.00'],
            ['   Precio: 1200.00'],
            ['   Activo: Si'],
            [''],
            ['NOTA: Mantenga esta hoja para referencia, no es necesario modificarla.'],
            ['Fecha de generación: ' . date('d/m/Y H:i:s')],
        ];
    }

    public function title(): string
    {
        return 'Instrucciones';
    }

    public function styles(Worksheet $sheet)
    {
        // Título principal
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '2E75B6']
            ]
        ]);

        // Encabezados de secciones
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'ED7D31']
            ]
        ]);

        $sheet->getStyle('A9')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'ED7D31']
            ]
        ]);

        $sheet->getStyle('A15')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'ED7D31']
            ]
        ]);

        $sheet->getStyle('A21')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'ED7D31']
            ]
        ]);

        $sheet->getStyle('A26')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'ED7D31']
            ]
        ]);

        $sheet->getStyle('A33')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FF0000']
            ]
        ]);

        // Autoajustar columnas
        $sheet->getColumnDimension('A')->setWidth(80);

        // Alinear texto
        $sheet->getStyle('A1:A35')->getAlignment()->setWrapText(true);

        return $sheet;
    }
}
