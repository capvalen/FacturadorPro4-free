@extends('tenant.layouts.app')

@section('content')

    <tenant-contingencies-index :is-client="{{ json_encode($is_client) }}"></tenant-contingencies-index>

@endsection

@push('scripts')
<script type="text/javascript">
	$(function(){
    'use strict';
        $(".tableScrollTop,.tableWide-wrapper").scroll(function(){
            $(".tableWide-wrapper,.tableScrollTop")
                .scrollLeft($(this).scrollLeft());
        });
    });
</script>
@endpush