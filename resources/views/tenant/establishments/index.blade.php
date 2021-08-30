@extends('tenant.layouts.app')

@section('content')

    <tenant-establishments-index :type-user="{{ json_encode(auth()->user()->type) }}"></tenant-establishments-index>

@endsection