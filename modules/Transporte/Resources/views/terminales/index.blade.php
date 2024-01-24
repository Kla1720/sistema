@extends('tenant.layouts.app')

@section('content')
    <tenant-transporte-terminales 
    :terminales='@json($terminales)' 
    :destinos='@json($destinos)' />
@endsection
