@extends('tenant.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-6 col-md-12 pt-2 pt-md-0">
            <tenant-companies-form></tenant-companies-form>
        </div>
        <div class="col-lg-6 col-md-12">
            <tenant-certificates-index></tenant-certificates-index>
        </div>
    </div>
    {{--<div class="row">--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-configurations-form></tenant-configurations-form>--}}
        {{--</div>--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-options-form></tenant-options-form>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-bank_accounts-index></tenant-bank_accounts-index>--}}
        {{--</div>--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-unit_types-index></tenant-unit_types-index>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-users-index></tenant-users-index>--}}
        {{--</div>--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-establishments-index></tenant-establishments-index>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-currency-types-index></tenant-currency-types-index>--}}
        {{--</div>--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-exchange_rates-index></tenant-exchange_rates-index>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-banks-index></tenant-banks-index>--}}
        {{--</div>--}}

        {{--<div class="col-lg-6 col-md-12">--}}
            {{--<tenant-attribute_types-index></tenant-attribute_types-index>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection
