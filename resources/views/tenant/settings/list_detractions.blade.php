@extends('tenant.layouts.app')

@section('content')
    <tenant-detraction_types-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-detraction_types-index>
@endsection
