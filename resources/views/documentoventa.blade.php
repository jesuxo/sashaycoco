@extends('layouts.master')
@section('title')
   @if($tipofac == 'A'  )
       VENTA NRO: {{$numerod}}
   @endif
   @if($tipofac == 'B'  )
        DEVOLUCION NRO: {{$numerod}}
   @endif
@endsection
@section('css')

@endsection
@section('content')
    @include('layouts.documento')
@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
