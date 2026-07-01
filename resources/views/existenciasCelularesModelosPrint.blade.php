@extends('layouts.master')
@section('title')
    Existencias de productos
@endsection
@section('css')
    <style>
        #clearall{
            text-decoration: none !important;
        }

        .tdline{
            border:1px solid #0072c5 !important;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            color: white !important;
            background-color: #0072c5 !important;
        }

        /* Estilos adicionales para mejorar la visualización */
        .table-matrix {
            font-size: 0.85rem;
        }

        .table-matrix thead th {
            vertical-align: middle;
            text-align: center;
            padding: 12px 8px;
        }

        .table-matrix tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 10px 8px;
        }

        .table-matrix tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .marca-nombre {
            font-weight: 600;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
        }

        .total-sucursal {
            background-color: #d4edda;
            font-weight: bold;
        }

        .total-general {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        /* Botones de ordenamiento */
        .sort-btn {
            cursor: pointer;
            transition: all 0.2s;
        }

        .sort-btn:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .sort-icon {
            font-size: 0.7rem;
            margin-left: 3px;
        }

        /* Badges */
        .badge-cantidad {
            font-size: 0.8rem;
            padding: 5px 10px;
        }

        /* Filtros */
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@endsection
@section('content')
    {!! $html !!}
@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
