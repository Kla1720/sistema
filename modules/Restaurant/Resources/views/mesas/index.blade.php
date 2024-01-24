@extends('tenant.layouts.app')

@section('content')
    <tenant-restaurant-mesas-index :mesas='@json($mesas)' :niveles='@json($niveles)'></tenant-restaurant-mesas-index>
@endsection
