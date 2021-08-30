@extends('tenant.layouts.app')

@section('content')
    <tenant-dispatches-index :type-user="{{ json_encode(auth()->user()->type) }}"></tenant-dispatches-index>
@endsection
