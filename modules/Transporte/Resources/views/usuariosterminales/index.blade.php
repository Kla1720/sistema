@extends('tenant.layouts.app')

@section('content')
    <tenant-transporte-usuariosterminales :usuarios_terminales='@json($usuarios_terminales)'></tenant-transporte-usuariosterminales>
@endsection
