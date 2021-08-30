@extends('tenant.layouts.app')

@section('content')
    <tenant-card-brands-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-card-brands-index>
@endsection
