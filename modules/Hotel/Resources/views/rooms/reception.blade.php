@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-reception :floors='@json($floors)' :room-status='@json($roomStatus)' :rooms='@json($rooms)'></tenant-hotel-reception>
@endsection
