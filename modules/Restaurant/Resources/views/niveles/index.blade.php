@extends('tenant.layouts.app')

@section('content')
    <tenant-restaurant-niveles-index :niveles='@json($niveles)'></tenant-restaurant-niveles-index>
@endsection
