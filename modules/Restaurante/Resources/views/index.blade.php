@extends('restaurante::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        Restaurante This view is loaded from module: {!! config('restaurante.name') !!}
    </p>
@endsection
