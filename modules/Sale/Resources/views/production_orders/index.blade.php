@extends('tenant.layouts.app')

@section('content')

    <tenant-production-orders-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-production-orders-index>

@endsection