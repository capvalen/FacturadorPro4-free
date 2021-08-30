@extends('tenant.layouts.app')

@section('content')

    <tenant-fixed-asset-items-index  :type-user="{{json_encode(Auth::user()->type)}}"></tenant-fixed-asset-items-index>

@endsection