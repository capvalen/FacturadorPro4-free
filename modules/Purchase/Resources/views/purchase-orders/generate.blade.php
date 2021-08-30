@extends('tenant.layouts.app')

@section('content')

    <tenant-purchase-orders-generate :purchase_quotation="{{ json_encode($purchase_quotation) }}"></tenant-purchase-orders-generate>

@endsection