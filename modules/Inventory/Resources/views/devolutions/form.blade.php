@extends('tenant.layouts.app')

@section('content')

    <tenant-inventory-devolutions-form :type-user="{{json_encode(Auth::user()->type)}}"></tenant-inventory-devolutions-form>

@endsection