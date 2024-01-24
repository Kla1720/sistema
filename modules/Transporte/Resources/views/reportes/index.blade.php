@extends('tenant.layouts.app')

@section('content')
    <tenant-transporte-reportes 
    :oficinas='@json($oficinas)'
    />
@endsection
