@extends('tenant.layouts.app')

@section('content')

    <tenant-sale-opportunities-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-sale-opportunities-index>

@endsection