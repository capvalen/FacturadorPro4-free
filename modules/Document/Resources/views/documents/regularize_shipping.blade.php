@extends('tenant.layouts.app')

@section('content')

    <tenant-documents-regularize-shipping :is-client="{{ json_encode($is_client) }}"></tenant-documents-regularize-shipping>

@endsection