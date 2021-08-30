@extends('tenant.layouts.app')

@section('content')

    <tenant-expenses-form :id="{{ json_encode($id) }}"></tenant-expenses-form>

@endsection