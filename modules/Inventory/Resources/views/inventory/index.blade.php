@extends('tenant.layouts.app')

@section('content')

    <inventory-index :type-user="{{ json_encode(auth()->user()->type) }}"></inventory-index>

@endsection
