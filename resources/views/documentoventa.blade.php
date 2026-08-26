@extends('layouts.master')
@section('title')
   @if($tipofac == 'A' or $tipofac == 'Z'  )
       VENTA NRO: {{$numerod}}
   @endif
   @if($tipofac == 'B'  or $tipofac == 'W'  )
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
