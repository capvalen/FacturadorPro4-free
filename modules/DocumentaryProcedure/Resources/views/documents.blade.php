@extends('tenant.layouts.app')

@section('content')
    <tenant-documentary-documents :documents='@json($documents)'></tenant-documentary-documents>
@endsection
