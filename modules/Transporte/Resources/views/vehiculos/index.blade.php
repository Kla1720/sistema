@extends('tenant.layouts.app')

@section('content')
    <tenant-Transporte-vehiculos :vehiculos='@json($vehiculos)'></tenant-Transporte-vehiculos>
@endsection
