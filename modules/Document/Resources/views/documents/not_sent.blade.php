@extends('tenant.layouts.app')

@section('content')

    <tenant-documents-not-sent :is-client="{{ json_encode($is_client) }}"></tenant-documents-not-sent>

@endsection