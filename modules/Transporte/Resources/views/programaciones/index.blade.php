@extends('tenant.layouts.app')

@section('content')
    <tenant-transporte-programaciones
    :terminales='@json($terminales)'
    :vehiculos='@json($vehiculos)'
    :series='@json($series)'
    :choferes='@json($choferes)'
    :user-terminal='@json($user_terminal)'
    />
@endsection
