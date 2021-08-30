@extends('tenant.layouts.app')

@section('content')

    <tenant-technical-services-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-technical-services-index>

@endsection