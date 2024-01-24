@extends('tenant.layouts.app')

@section('content')
    <tenant-Transporte-choferes :choferes='@json($choferes)'></tenant-Transporte-choferes>
@endsection
