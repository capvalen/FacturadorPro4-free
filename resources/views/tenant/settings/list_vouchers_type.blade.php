@extends('tenant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12 ui-sortable">
        <tenant-income-types-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-income-types-index>
    </div>
    <div class="col-12 ui-sortable">
        <tenant-expense-types-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-expense-types-index>
    </div>
</div>
@endsection
