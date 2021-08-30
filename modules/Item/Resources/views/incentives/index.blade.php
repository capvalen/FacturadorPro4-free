@extends('tenant.layouts.app')

@section('content')

    <tenant-incentives-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-incentives-index>

@endsection