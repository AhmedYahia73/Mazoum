@php
    $lang = app()->getLocale();
@endphp

@extends('user.layouts.master')


@section('header')
	<style>
      .main-banner.inner-banner:before , .site-branding a{
      	display:none
      }
	</style>
@endsection


@section('content')


    <!-- Banner Start -->
    <section class="main-banner inner-banner back-img" id="main-banner" style="background-image: url('{{ asset('logo/desgins.jpg') }}');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="banner-content"  style="display:none">
                        <h1 class="h1-title wow fadeup-animation" data-wow-delay="0.2s"> {{ trans('home.Desgins') }} </h1>
                        <div class="breadcrumb-box wow fadeup-animation" data-wow-delay="0.4s">
                            <ul>
                                <li><a href="{{ route($lang.'_home') }}" title="Home"> {{ trans('home.Home') }} </a></li>
                                <li>{{ trans('home.Desgins') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner End -->

    @php
        $Desgins = App\Models\WebDesgins::get();
    @endphp

    @if($Desgins != null && $Desgins->count() > 0)
	<!-- Portfolio Tabbing Start -->
	<div class="main-portfolio-tabbing">
		<div class="container">

            @include('user.includes.desgins')

		</div>
	</div>
	<!-- Portfolio Tabbing End -->
    @endif


@endsection
