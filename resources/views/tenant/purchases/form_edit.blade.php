@extends('tenant.layouts.app')

@section('content')
 
    <tenant-purchases-edit :resource-id="{{json_encode($resourceId)}}"></tenant-purchases-edit>

@endsection