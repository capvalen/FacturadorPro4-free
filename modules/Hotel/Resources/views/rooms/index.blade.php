@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-rooms :floors='@json($floors)' :room-status='@json($roomStatus)' :categories='@json($categories)' :rooms='@json($rooms)'></tenant-hotel-rooms>
@endsection
