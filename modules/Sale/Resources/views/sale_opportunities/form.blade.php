@extends('tenant.layouts.app')

@section('content')
    <tenant-sale-opportunities-form :type-user="{{json_encode(Auth::user()->type)}}" :id="{{json_encode($id)}}"></tenant-sale-opportunities-form>
@endsection
