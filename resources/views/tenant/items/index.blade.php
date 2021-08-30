@extends('tenant.layouts.app')

@section('content')
    <tenant-items-index type="{{ $type ?? '' }}" :type-user="{{json_encode(Auth::user()->type)}}"></tenant-items-index>
@endsection
