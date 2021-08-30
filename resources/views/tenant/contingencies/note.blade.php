@extends('tenant.layouts.app')

@section('content')

    <tenant-documents-note :document="{{ json_encode($document) }}"></tenant-documents-note>

@endsection