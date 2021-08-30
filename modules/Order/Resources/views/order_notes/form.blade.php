@extends('tenant.layouts.app')

@section('content')
    <tenant-order-notes-form :type-user="{{json_encode(Auth::user()->type)}}"
                             :configuration="{{$configuration}}"></tenant-order-notes-form>
@endsection
