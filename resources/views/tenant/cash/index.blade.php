@extends('tenant.layouts.app')

@section('content')

    <cash-index :type-user="{{json_encode(Auth::user()->type)}}"  ></cash-index>

@endsection
