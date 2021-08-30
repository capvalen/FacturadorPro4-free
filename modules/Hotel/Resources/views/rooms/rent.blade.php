@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-rent :room='@json($room)' :affectation-igv-types='@json($affectation_igv_types)'></tenant-hotel-rent>
@endsection
