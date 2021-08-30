@extends('tenant.layouts.app')

@section('content')

    <tenant-finance-payment-method-types-index :configuration="{{ $configuration }}"></tenant-finance-payment-method-types-index>

@endsection
