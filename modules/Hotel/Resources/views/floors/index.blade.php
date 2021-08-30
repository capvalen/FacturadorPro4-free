@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-floors :floors='@json($floors)'></tenant-hotel-floors>
@endsection
