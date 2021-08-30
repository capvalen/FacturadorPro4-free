@extends('tenant.layouts.app')

@section('content')

    <tenant-dispatchers-index :type-user="{{json_encode(Auth::user()->type)}}"></tenant-dispatchers-index>

@endsection
