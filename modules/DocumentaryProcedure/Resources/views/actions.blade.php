@extends('tenant.layouts.app')

@section('content')
    <tenant-documentary-actions :actions='@json($actions)'></tenant-documentary-actions>
@endsection
