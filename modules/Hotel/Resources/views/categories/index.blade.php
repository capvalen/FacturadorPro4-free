@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-categories :categories='@json($categories)'></tenant-hotel-categories>
@endsection
