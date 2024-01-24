@extends('tenant.layouts.app')

@section('content')
    <tenant-transporte-destinos :destinos='@json($destinos)'></tenant-transporte-destinos>
@endsection
