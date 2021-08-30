@extends('tenant.layouts.app')

@section('content')
    <tenant-order-notes-edit :resource-id="{{json_encode($resourceId)}}" :type-user="{{json_encode(Auth::user()->type)}}"></tenant-order-notes-edit>
@endsection
