@extends('tenant.layouts.app')

@section('content')
    <tenant-transporte-encomiendas-manifiesto
    :manifiesto="{{$manifiesto}}"
    
    />

@endsection
