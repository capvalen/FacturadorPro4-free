@extends('tenant.layouts.app')

@section('content')
    <tenant-currency-types-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-currency-types-index>
@endsection
