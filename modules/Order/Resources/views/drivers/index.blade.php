@extends('tenant.layouts.app')

@section('content')

    <tenant-drivers-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-drivers-index>

@endsection
