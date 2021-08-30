@extends('tenant.layouts.app')

@section('content')

    <tenant-dashboard-index
    	:type-user="{{ json_encode(auth()->user()->type) }}"
    	:soap-company="{{ json_encode($soap_company) }}">
    </tenant-dashboard-index>

@endsection
