@extends('tenant.layouts.app')

@section('content')
    <tenant-unit_types-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-unit_types-index>
@endsection
