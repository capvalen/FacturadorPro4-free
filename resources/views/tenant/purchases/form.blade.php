@extends('tenant.layouts.app')

@section('content')
 
    <tenant-purchases-form :purchase_order_id="{{ json_encode($purchase_order_id) }}"></tenant-purchases-form>

@endsection