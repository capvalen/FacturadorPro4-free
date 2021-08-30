@extends('tenant.layouts.app')

@section('content') 
    <tenant-order-forms-form :id="{{ json_encode($id) }}" ></tenant-order-forms-form>
@endsection
