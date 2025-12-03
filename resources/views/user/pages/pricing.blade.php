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
	<section class="main-banner inner-banner back-img" id="main-banner" style="background-image: url('{{ asset('logo/pricing.jpg') }}');">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="banner-content"  style="display:none">
						<h1 class="h1-title wow fadeup-animation" data-wow-delay="0.2s"> {{ trans('home.Pricing') }} </h1>
						<div class="breadcrumb-box wow fadeup-animation" data-wow-delay="0.4s">
							<ul>
								<li><a href="{{ route($lang.'_home') }}" title="Home"> {{ trans('home.Home') }} </a></li>
								<li>{{ trans('home.Pricing') }}</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Banner End -->

    @php
        $Pricing = App\Models\Pricing::get();
    @endphp

	<!-- Our Pricing Start -->
	<section class="main-pricing page-pricing" style="padding-bottom: 100px">
		<div class="container">

            @include('user.includes.pricing')

		</div>
	</section>
	<!-- Our Pricing End -->

@endsection
