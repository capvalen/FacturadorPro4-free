@extends('tenant.layouts.app')

@section('content')

<div id="extern-query">
    <generar-n-boletas :is-client="{{ json_encode($is_client) }}"
                            :type-user="{{ json_encode(auth()->user()->type) }}"
                            :import_documents="{{ json_encode($import_documents) }}"
                            user-id="{{ auth()->user()->id }}"
                            :import_documents_second="{{ json_encode($import_documents_second) }}"></generar-n-boletas>
</div>
    

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
<script src="{{ asset('/js/externquery.js') }}"></script>
@endpush