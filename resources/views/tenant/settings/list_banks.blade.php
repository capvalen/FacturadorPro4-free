@extends('tenant.layouts.app')

@section('content')
    <tenant-banks-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-banks-index>
@endsection
