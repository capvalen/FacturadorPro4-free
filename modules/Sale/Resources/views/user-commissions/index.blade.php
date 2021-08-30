@extends('tenant.layouts.app')

@section('content')

    <tenant-user-commissions-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-user-commissions-index>

@endsection