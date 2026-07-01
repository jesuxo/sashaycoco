<?php

namespace App\Exports;

use App\Models\Cwtransferencia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransferenciasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $transferencias;
    protected $filtros;

    public function __construct($transferencias, $filtros = [])
    {
        $this->transferencias = $transferencias;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        return collect($this->transferencias);
    }

    public function headings(): array
    {
        return [
            'ID',
            'FECHA',
            'NÚMERO',
            'TITULAR',
            'BANCO',
            'SUCURSAL',
            'MONEDA',
            'MONTO',
            'ESTADO',
            'OBSERVACIÓN',
            'FECHA CREACIÓN',
            'IMAGEN'
        ];
    }

    public function map($transferencia): array
    {
        // Determinar moneda
        $moneda = '';
        if ($transferencia->bs) $moneda = 'Bs';
        if ($transferencia->dolares) $moneda = 'USD';
        if ($transferencia->pesos) $moneda = 'COP';

        // Determinar estado
        $estado = match($transferencia->status) {
            0 => 'PENDIENTE',
            1 => 'APROBADA',
            2 => 'RECHAZADA',
            default => 'DESCONOCIDO'
        };

        return [
            $transferencia->id,
            $transferencia->fechaformat,
            $transferencia->numero,
            $transferencia->titular,
            $transferencia->banco->descrip ?? 'N/A',
            $transferencia->sucursal->descrip ?? 'N/A',
            $moneda,
            $transferencia->monto,
            $estado,
            $transferencia->observacion ?? '',
            $transferencia->created_at->format('d/m/Y H:i'),
            $transferencia->imagen ? 'SÍ' : 'NO'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0072C5']]],
        ];
    }

    public function title(): string
    {
        return 'Transferencias';
    }
}
