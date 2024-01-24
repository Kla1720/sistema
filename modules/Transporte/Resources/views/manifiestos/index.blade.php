@extends('tenant.layouts.app')

@section('content')
    <tenant-transporte-manifiestos
    :series='@json($series)'
    :choferes='@json($choferes)'
    />

@endsection
