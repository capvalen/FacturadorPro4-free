@extends('tenant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12 ui-sortable">
        <tenant-expense-reasons-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-expense-reasons-index>
    </div>
    <div class="col-12 ui-sortable">
        <tenant-income-reasons-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-income-reasons-index>
    </div>
</div>
@endsection
