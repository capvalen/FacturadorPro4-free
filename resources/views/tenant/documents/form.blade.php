@extends('tenant.layouts.app')

@push('styles')
    <style type="text/css">
        .v-modal {
            opacity: 0.2 !important;
        }
        .border-custom {
            border-color: rgba(0,136,204, .5) !important;
        }
        @media only screen and (min-width: 768px) {
        	.inner-wrapper {
			    padding-top: 60px !important;
			}
        }
        .card-header {
		    border-radius: 0px 0px 0px !important;
		}
    </style>
@endpush

@section('content')
    <tenant-documents-invoice :is_contingency="{{ json_encode($is_contingency) }}" :type-user="{{json_encode(Auth::user()->type)}}" :configuration="{{ $configuration }}" :document-id="{{ $documentId ?? 0 }}" :is-update="{{ json_encode($isUpdate ?? false) }}" :id-user="{{json_encode(Auth::user()->id)}}"></tenant-documents-invoice>
@endsection

@push('scripts')
<script type="text/javascript">
	var count = 0;
	$(document).on("click", "#card-click", function(event){
		count = count + 1;
		if (count == 1) {
			$("#card-section").removeClass("card-collapsed");
		}
	});
</script>
@endpush
