<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EstadisticasTransferenciasExport implements WithMultipleSheets
{
    protected $estadisticas;
    protected $filtros;

    public function __construct($estadisticas, $filtros)
    {
        $this->estadisticas = $estadisticas;
        $this->filtros = $filtros;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Hoja de resumen general
        $sheets[] = new class($this->estadisticas, $this->filtros) implements FromArray, WithHeadings, WithTitle {
            protected $estadisticas;
            protected $filtros;

            public function __construct($estadisticas, $filtros)
            {
                $this->estadisticas = $estadisticas;
                $this->filtros = $filtros;
            }

            public function array(): array
            {
                $data = [];

                // Información de filtros
                $data[] = ['REPORTE DE ESTADÍSTICAS DE TRANSFERENCIAS'];
                $data[] = ['Fecha de generación: ' . now()->format('d/m/Y H:i:s')];
                $data[] = ['Filtros aplicados:'];
                if (!empty($this->filtros['fechas'])) {
                    $data[] = ['- Período: ' . str_replace('to', ' al ', $this->filtros['fechas'])];
                }
                if (!empty($this->filtros['sucursal'])) {
                    $data[] = ['- Sucursal: ' . $this->filtros['sucursal']];
                }
                if (!empty($this->filtros['moneda'])) {
                    $moneda = match($this->filtros['moneda']) {
                        'bs' => 'Bolívares',
                        'usd' => 'Dólares',
                        'cop' => 'Pesos',
                        default => 'Todas'
                    };
                    $data[] = ['- Moneda: ' . $moneda];
                }
                $data[] = [];

                // Resumen general
                $data[] = ['RESUMEN GENERAL'];
                $data[] = ['Total Transferencias', $this->estadisticas['generales']['total']];
                $data[] = ['Pendientes', $this->estadisticas['generales']['pendientes']];
                $data[] = ['Aprobadas', $this->estadisticas['generales']['aprobadas']];
                $data[] = ['Rechazadas', $this->estadisticas['generales']['rechazadas']];
                $data[] = ['Monto Total', $this->estadisticas['generales']['monto_total']];
                $data[] = ['Tasa de Aprobación', number_format($this->estadisticas['generales']['tasa_aprobacion'], 2) . '%'];
                $data[] = ['Tiempo Promedio Aprobación', $this->estadisticas['generales']['tiempo_promedio_aprobacion'] . ' horas'];
                $data[] = [];

                // Por moneda
                $data[] = ['DETALLE POR MONEDA'];
                $data[] = ['Moneda', 'Cantidad', 'Monto', 'Aprobadas', '% Aprobación'];

                $bs = $this->estadisticas['por_moneda']['bs'];
                $usd = $this->estadisticas['por_moneda']['usd'];
                $cop = $this->estadisticas['por_moneda']['cop'];

                $data[] = ['Bolívares', $bs['cantidad'], $bs['monto'], $bs['aprobadas'], $bs['cantidad'] > 0 ? round(($bs['aprobadas']/$bs['cantidad'])*100, 2) . '%' : '0%'];
                $data[] = ['Dólares', $usd['cantidad'], $usd['monto'], $usd['aprobadas'], $usd['cantidad'] > 0 ? round(($usd['aprobadas']/$usd['cantidad'])*100, 2) . '%' : '0%'];
                $data[] = ['Pesos', $cop['cantidad'], $cop['monto'], $cop['aprobadas'], $cop['cantidad'] > 0 ? round(($cop['aprobadas']/$cop['cantidad'])*100, 2) . '%' : '0%'];
                $data[] = [];

                // Proyecciones
                $data[] = ['PROYECCIONES'];
                $data[] = ['Proyección Mensual', $this->estadisticas['proyecciones']['proyeccion_mensual'] . ' transferencias'];
                $data[] = ['Meta de Aprobación', $this->estadisticas['proyecciones']['meta_aprobacion'] . '%'];
                $data[] = ['Días para alcanzar meta', $this->estadisticas['proyecciones']['dias_para_meta'] . ' días'];

                return $data;
            }

            public function headings(): array
            {
                return [];
            }

            public function title(): string
            {
                return 'Resumen';
            }
        };

        // Hoja de top bancos
        $sheets[] = new class($this->estadisticas) implements FromArray, WithHeadings, WithTitle {
            protected $estadisticas;

            public function __construct($estadisticas)
            {
                $this->estadisticas = $estadisticas;
            }

            public function array(): array
            {
                $data = [];
                foreach ($this->estadisticas['top_bancos'] as $banco) {
                    $porcAprobacion = 0;
                    if(isset($banco['cantidad']) and $banco['cantidad'] > 0) {
                       $porcAprobacion = $banco['cantidad'] > 0 ? ($banco['aprobadas'] / $banco['cantidad'] * 100) : 0;
                    }
                    $data[] = [
                        (isset($banco['nombre']))?$banco['nombre'] : '',
                        (isset($banco['cantidad']))?$banco['cantidad'] : 0,
                        (isset($banco['monto']))?$banco['monto'] : 0,
                        (isset($banco['aprobadas']))?$banco['aprobadas'] : 0,
                        number_format($porcAprobacion, 2) . '%'
                    ];
                }
                return $data;
            }

            public function headings(): array
            {
                return ['Banco', 'Transferencias', 'Monto Total', 'Aprobadas', '% Aprobación'];
            }

            public function title(): string
            {
                return 'Top Bancos';
            }
        };

        // Hoja de top clientes
        $sheets[] = new class($this->estadisticas) implements FromArray, WithHeadings, WithTitle {
            protected $estadisticas;

            public function __construct($estadisticas)
            {
                $this->estadisticas = $estadisticas;
            }

            public function array(): array
            {
                $data = [];
                foreach ($this->estadisticas['top_clientes'] as $cliente) {

                    $div = 0;
                    if(isset($cliente['cantidad']) and $cliente['cantidad'] > 0) {
                        $div = $cliente['monto'] / $cliente['cantidad'];
                    }

                    $data[] = [
                        (isset($cliente['titular']))?$cliente['titular'] : '',
                        (isset($cliente['cantidad']))?$cliente['cantidad'] : 0,
                        (isset($cliente['monto']))?$cliente['monto'] : 0,
                        number_format($div, 2)
                    ];
                }
                return $data;
            }

            public function headings(): array
            {
                return ['Cliente', 'Transferencias', 'Monto Total', 'Promedio por Transf'];
            }

            public function title(): string
            {
                return 'Top Clientes';
            }
        };

        return $sheets;
    }
}
