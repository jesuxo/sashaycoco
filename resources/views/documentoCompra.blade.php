@extends('layouts.master')
@section('title')
    Compra NRO: {{$numerod}}
@endsection
@section('css')

@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-12">
            <div class="card " id="demo">
                @include('layouts.documentoSacomp')
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
