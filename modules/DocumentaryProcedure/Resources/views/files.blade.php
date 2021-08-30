@extends('tenant.layouts.app')

@section('content')
    <tenant-documentary-files :files='@json($files)' :offices='@json($offices)'></tenant-documentary-files>
@endsection
