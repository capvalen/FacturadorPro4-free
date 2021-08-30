@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-rates :rates='@json($rates)'></tenant-hotel-rates>
@endsection
