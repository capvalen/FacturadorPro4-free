@extends('tenant.layouts.app')

@section('content')
<tenant-hotel-rent-checkout :room='@json($room)' token="{{ $token }}" :customer='@json($customer)' :rent='@json($rent)' :payment-method-types='{{ $payment_method_types }}' :payment-destinations='{{ $payment_destinations }}' :all-series='{{ $series }}' :document-types-invoice='{{ $document_types_invoice }}' warehouse-id="{{ $warehouse_id }}">
</tenant-hotel-rent-checkout>
@endsection
