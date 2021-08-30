@extends('tenant.layouts.app')

@section('content')
    <tenant-quotations-edit :resource-id="{{json_encode($resourceId)}}" :type-user="{{json_encode(Auth::user()->type)}}"></tenant-quotations-edit>
@endsection
