@extends('tenant.layouts.app')

@section('content')

    <tenant-sale-notes-index :soap-company="{{ json_encode($soap_company) }}" :type-user="{{ json_encode(auth()->user()->type) }}" :configuration="{{ $configuration }}"></tenant-sale-notes-index>

@endsection
