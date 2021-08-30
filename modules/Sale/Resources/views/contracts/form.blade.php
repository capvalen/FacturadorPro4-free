@extends('tenant.layouts.app')

@section('content')
    <tenant-contracts-form :type-user="{{json_encode(Auth::user()->type)}}"
     :id="{{json_encode($id)}}"
     :quotation-id="{{json_encode($quotationId)}}"
     :show-payments="{{json_encode($showPayments)}}"
     ></tenant-contracts-form>
@endsection
