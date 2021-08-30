@extends('tenant.layouts.app')

@section('content')

    <tenant-report-documents-index
        :configuration='@json($configuration)'
    ></tenant-report-documents-index>

@endsection
