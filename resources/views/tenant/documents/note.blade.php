@extends('tenant.layouts.app')

@section('content')

    <tenant-documents-note :user="{{ json_encode(auth()->user()) }}" :document_affected="{{ json_encode($document_affected) }}" :configuration="{{ $configuration }}"></tenant-documents-note>

@endsection
