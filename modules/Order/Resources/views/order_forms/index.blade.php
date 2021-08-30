@extends('tenant.layouts.app')

@section('content')
    <tenant-order-forms-index :type-user="{{ json_encode(auth()->user()->type) }}"></tenant-order-forms-index>
@endsection
