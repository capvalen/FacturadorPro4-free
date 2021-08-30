@extends('tenant.layouts.app')

@section('content')
    <tenant-web-platforms-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-web-platforms-index>
@endsection
