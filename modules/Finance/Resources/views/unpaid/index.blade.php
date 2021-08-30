@extends('tenant.layouts.app')

@section('content')

    <tenant-finance-unpaid-index :type-user="{{ json_encode(auth()->user()->type) }}" ></tenant-finance-unpaid-index>

@endsection
