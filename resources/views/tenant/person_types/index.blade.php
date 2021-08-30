@extends('tenant.layouts.app')

@section('content')

    <tenant-person-types-index :type-user="{{json_encode(Auth::user()->type)}}"  ></tenant-person-types-index>

@endsection