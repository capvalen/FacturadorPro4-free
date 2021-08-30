@extends('tenant.layouts.app')

@section('content')
    <tenant-orders-index :user="{{ json_encode(auth()->user()) }}"></tenant-orders-index>
@endsection
