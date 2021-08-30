@extends('tenant.layouts.app')

@section('content')
    <tenant-bank_accounts-index :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-bank_accounts-index>
@endsection
