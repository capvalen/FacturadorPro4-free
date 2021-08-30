@extends('tenant.layouts.app')

@section('content')

    <tenant-sale-notes-form :id="{{ json_encode($id) }}" :type-user="{{json_encode(Auth::user()->type)}}" ></tenant-sale-notes-form>

@endsection
