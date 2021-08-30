@extends('tenant.layouts.app')

@section('content')

    <tenant-finance-balance-index
        :user='@json(Auth::user())'
        :configuration='@json($configuration)'
    >
    </tenant-finance-balance-index>

@endsection