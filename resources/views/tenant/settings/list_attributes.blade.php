@extends('tenant.layouts.app')

@section('content')
    <tenant-attribute_types-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-attribute_types-index>
@endsection
