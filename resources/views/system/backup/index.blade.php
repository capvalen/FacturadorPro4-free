@extends('system.layouts.app')

@section('content')
	<system-backup :disc-used="{{json_encode($disc_used)}}" :storage-size="{{json_encode($storage_size)}}" :last-zip="{{json_encode($last_zip)}}" :clients='@json($clients)'></system-backup>
@endsection
