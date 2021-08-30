@extends('tenant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12 ui-sortable">
        <tenant-expense-method-types-index :type-user="{{json_encode(Auth::user()->type)}}">
        </tenant-expense-method-types-index>
    </div>
    <div class="col-12 ui-sortable">
        <tenant-payment-method-types-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-payment-method-types-index>
    </div>
</div>
@endsection
