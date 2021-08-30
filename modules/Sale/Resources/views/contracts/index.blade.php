@extends('tenant.layouts.app')

@section('content')

    <tenant-contracts-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-contracts-index>

@endsection