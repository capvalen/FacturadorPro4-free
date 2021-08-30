@extends('tenant.layouts.app')

@section('content')
 
    <tenant-fixed-asset-purchases-form :id="{{ json_encode($id) }}"></tenant-fixed-asset-purchases-form>

@endsection