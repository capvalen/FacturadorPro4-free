@extends('tenant.layouts.app')

@section('content')

    <tenant-purchase-orders-form :id="{{ json_encode($id) }}" :sale-opportunity="{{ json_encode($sale_opportunity) }}"></tenant-purchase-orders-form>

@endsection