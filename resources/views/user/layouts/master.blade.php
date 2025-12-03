@php $lang = 'ar'; @endphp

<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
	<title>Mazoom</title>
	<meta charset="utf-8">
	<meta name="keywords" content="Mazoom" />
    <meta name="description" content="Mazoom" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  
  
  	<meta name="facebook-domain-verification" content="znwsakdp9gljom60umh4ysh621p4lx" />

	<!-- FavIcon Link -->

	<!-- Bootstrap CSS Link -->
	<link rel="stylesheet" type="text/css" href="{{ asset('website/assets') }}/css/bootstrap.min.css">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

	<!-- Font Awesome Icon CSS Link -->
	<link rel="stylesheet" type="text/css" href="{{ asset('website/assets') }}/css/font-awesome.min.css">

	<!-- Slick Slider CSS Link -->
	<link rel="stylesheet" type="text/css" href="{{ asset('website/assets') }}/css/slick.css">
	<link rel="stylesheet" type="text/css" href="{{ asset('website/assets') }}/css/slick-theme.css">

	<!-- Magnific Popup -->
	<link href="{{ asset('website/assets') }}/vendor/magnific-popup/magnific-popup.min.css" rel="stylesheet">

	<!-- Wow Animation CSS Link -->
	<link rel="stylesheet" type="text/css" href="{{ asset('website/assets') }}/css/animate.css">

	<!-- Main Style CSS Link -->
	<link rel="stylesheet" type="text/css" href="{{ asset('website/assets') }}/css/style.css?{{ rand() }}">

    <link rel="stylesheet" href="{{asset('bootstrap-sweetalert')}}/sweetalert.css" />
    <link rel="stylesheet" href="{{ asset('arabic_font') }}/droidarabickufi.css"/>

    <style>

        body {
            direction: rtl;
            text-align: right;
        }

        body, html, h1, h2, h3, h4, h5, h6, p, li, .btn ,select , .btn-primary,
        .theme-green .back-bar .pointer-label , .main-navigation ul li a
        {
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
        }

        ::-webkit-input-placeholder {
            /* Chrome/Opera/Safari */
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
        }
        ::-moz-placeholder {
            /* Firefox 19+ */
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
        }
        :-ms-input-placeholder {
            /* IE 10+ */
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
        }
        :-moz-placeholder {
            /* Firefox 18- */
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
        }

    </style>

    <style>
        .pricing-box {
            padding: 33px 50px 40px 50px !important;
        }
		
        /*
		.portfolio-wrapper {
			height: 800px !important;
		}
        */
      
      	.h2-title {
          font-size: 35px !important;
        }
      
        .about-text ul li:nth-child(odd) .icon , .about-text ul li:nth-child(even) .icon {
            background: #546E70;
        }
      
        .main-testimonial-slider {
			background:none !important
        }
      
      	.service-box-text p {
          text-align: justify;
        }
      
      	.pricing-box:hover {
            box-shadow: none;
        }
      
      .pricing-text ul li:before {
      	display:none
      }
      
      .pricing-text ul li {
          padding-right: 0;
      }
      
      .main-pricing .icon {
          background: #546E70;
          width: 35px;
          height: 35px;
          display: inline-flex;
          justify-content: center;
          align-items: center;
          text-align: center;
          color: #ffffff;
          border-radius: 50%;
          margin-left: 10px;
      }
      
      .pricing-slider {
      	direction:ltr
      }
      
      .pricing-text ul {
        direction: rtl;
      }

      @media(max-width:768px) {
      
        .site-branding a img {
        	height: 55px;
        }
      }

    </style>

    @yield('header')

</head>
<body class="home">
<div class="main">


	<!-- Loder Start -->
	<div class="loader-box">
		<div class="loader-design">
			<svg class="gegga">
				<defs>
					<filter id="gegga">
						<feGaussianBlur in="SourceGraphic" stdDeviation="7" result="blur" />
						<feColorMatrix in="blur" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 20 -10" result="inreGegga" />
						<feComposite in="SourceGraphic" in2="inreGegga" operator="atop" />
					</filter>
				</defs>
			</svg>
			<svg class="snurra" width="200" height="200" viewBox="0 0 200 200">
				<defs>
					<linearGradient id="linjärGradient">
						<stop class="stopp1" offset="0" />
						<stop class="stopp2" offset="1" />
					</linearGradient>
					<linearGradient y2="160" x2="160" y1="40" x1="40" gradientUnits="userSpaceOnUse" id="gradient" xlink:href="#linjärGradient" />
				</defs>
				<path class="halvan" d="m 164,100 c 0,-35.346224 -28.65378,-64 -64,-64 -35.346224,0 -64,28.653776 -64,64 0,35.34622 28.653776,64 64,64 35.34622,0 64,-26.21502 64,-64 0,-37.784981 -26.92058,-64 -64,-64 -37.079421,0 -65.267479,26.922736 -64,64 1.267479,37.07726 26.703171,65.05317 64,64 37.29683,-1.05317 64,-64 64,-64"
				/>
				<circle class="strecken" cx="100" cy="100" r="64" />
			</svg>
			<svg class="skugga" width="200" height="200" viewBox="0 0 200 200">
				<path class="halvan" d="m 164,100 c 0,-35.346224 -28.65378,-64 -64,-64 -35.346224,0 -64,28.653776 -64,64 0,35.34622 28.653776,64 64,64 35.34622,0 64,-26.21502 64,-64 0,-37.784981 -26.92058,-64 -64,-64 -37.079421,0 -65.267479,26.922736 -64,64 1.267479,37.07726 26.703171,65.05317 64,64 37.29683,-1.05317 64,-64 64,-64"
				/>
				<circle class="strecken" cx="100" cy="100" r="64" />
			</svg>
		</div>
	</div>
	<!-- Loder End -->


	<!-- Header Start -->
	<header class="site-header">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-12">
					<div class="header-box">
						<div class="site-branding">
							<a href="{{ route($lang.'_home') }}" title="Mazoom">
								<img src="{{ asset('logo') }}/mazoom.png?{{ rand() }}" alt="Logo">
							</a>
						</div>
						<div class="header-menu">
							<nav class="main-navigation">
								<button class="toggle-button">
									<span></span>
									<span></span>
									<span></span>
								</button>
								<ul class="menu">
									<li class="{{ Route::is($lang.'_home') ? 'active' : '' }}">
                                        <a href="{{ route($lang.'_home') }}" title="Home">
                                            {{ trans('home.Home') }}
                                        </a>
                                    </li>

									<li class="{{ Route::is($lang.'-about-us') ? 'active' : '' }}">
                                        <a href="{{ route($lang.'-about-us') }}" title="About Us">
                                            {{ trans('home.About Us') }}
                                        </a>
                                    </li>

									<li class="{{ Route::is($lang.'_advantages') ? 'active' : '' }}">
                                        <a href="{{ route($lang.'_advantages') }}" title="Services">
                                            {{ trans('home.advantages') }}
                                        </a>
                                    </li>

									<li class="{{ Route::is($lang.'_portfolio') ? 'active' : '' }}">
                                        <a href="{{ route($lang.'_portfolio') }}" title="Services">
                                            {{ trans('home.Desgins') }}
                                        </a>
                                    </li>

									<li class="{{ Route::is($lang.'_pricing') ? 'active' : '' }}">
                                        <a href="{{ route($lang.'_pricing') }}" title="Services">
                                            {{ trans('home.Pricing') }}
                                        </a>
                                    </li>

									<li class="{{ Route::is($lang.'_view_contact_us') ? 'active' : '' }}">
                                        <a href="{{ route($lang.'_view_contact_us') }}" title="Contact Us">
                                            {{ trans('home.Contact Us') }}
                                        </a>
                                    </li>
								</ul>
							</nav>
							<div class="black-shadow"></div>
						</div>

						<div class="header-search">
							<a href="{{ route($lang.'_view_contact_us') }}" class="sec-btn" title="{{ trans('home.Get Started') }}">
                                <span>
                                    {{ trans('home.Get Started') }}
                                </span>
                            </a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header End -->


    @yield('content')


	<!-- Footer CSS Start -->
	<footer class="site-footer back-img" style="background-image: url('{{ asset('logo/footer.jpg') }}');">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<div class="newsletter">
						<h2 class="h2-title" style="font-size: 35px;"> {{ $lang == 'en' ? 'Subscribe To Our Newsletter For Latest Update' : 'اشترك في النشرة الإخبارية لدينا للحصول على آخر التحديثات' }} </h2>
						<div class="newsletter-form">

							<form class="contact-form" action="{{ route($lang.'-save-subscribe') }}" id="save-subscriber" method="post">

                                {{csrf_field()}}

                                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ $lang == 'en' ? 'Email Address' : 'البريد الألكتروني' }}" required>
								<button form="save-subscriber" value="Submit" type="submit" class="sec-btn"><span> {{ $lang == 'en' ? 'Subscribe Now' : 'أشترك الأن' }} </span></button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="row align-items-center">
				<div class="col-lg-4">
					<div class="footer-logo">
						<a href="{{ route($lang.'_home') }}" title="Mazoom" style="color: #FFF;width: auto;">
                      		معزوم الكويت للدعوات الالكتروانيه
                        </a>
					</div>
				</div>
				<div class="col-lg-8">
					<div class="footer-menu">
						<ul>
							<li><a href="{{ route($lang.'_home') }}" title="{{ trans('home.Home') }}"> {{ trans('home.Home') }} </a></li>
							<li><a href="{{ route($lang.'-about-us') }}" title="{{ trans('home.About Us') }}"> {{ trans('home.About Us') }} </a></li>
							<li><a href="{{ route($lang.'_advantages') }}" title="{{ trans('home.advantages') }}"> {{ trans('home.advantages') }} </a></li>
							<li><a href="{{ route($lang.'_portfolio') }}" title="{{ trans('home.Desgins') }}"> {{ trans('home.Desgins') }} </a></li>
							<li><a href="{{ route($lang.'_pricing') }}" title="{{ trans('home.Pricing') }}"> {{ trans('home.Pricing') }} </a></li>
							<li><a href="{{ route($lang.'_view_contact_us') }}" title="{{ trans('home.Contact Us') }}"> {{ trans('home.Contact Us') }} </a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="footer-bottom">
				<div class="row align-items-center">
					<div class="col-lg-8 order-lg-1 order-2">
						<div class="copy-right">
							<p>
                                Copyright © {{ Date('Y') }}
                                <a href="" target="_blank">
                                    Mazoom
                                </a>
                                . All rights reserved.
                            </p>
						</div>
					</div>
					<div class="col-lg-4 order-lg-2 order-1">
						<div class="social-icon">
							<a href="https://www.facebook.com/Mazom.kwt/" title="Follow on Facebook" tabindex="-1"><i class="fa fa-facebook" aria-hidden="true"></i></a>
							<a href="https://www.instagram.com/mazoom.kwt/?igsh=cGZtbzJwZzRubnZp&utm_source=qr" title="Follow on Instagram" tabindex="0"><i class="fa fa-instagram" aria-hidden="true"></i></a>
							<a href="https://api.whatsapp.com/send/?phone=96597378181&text&app_absent=0" title="Follow on whatsapp" tabindex="0"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- Footer CSS End -->


	<!-- Scroll To Top Start -->
	<a href="#main-banner" class="scroll-top" id="scroll-to-top">
		<i class="fa fa-angle-up" aria-hidden="true"></i>
	</a>
	<!-- Scroll To Top End-->

	<!-- Bubbles Animation Start -->
	<div class="bubbles_wrap">
		<div class="bubble x1"></div>
		<div class="bubble x2"></div>
		<div class="bubble x3"></div>
		<div class="bubble x4"></div>
		<div class="bubble x5"></div>
		<div class="bubble x6"></div>
		<div class="bubble x7"></div>
		<div class="bubble x8"></div>
		<div class="bubble x9"></div>
		<div class="bubble x10"></div>
	</div>
	<!-- Bubbles Animation End-->
</div>


    <!-- Jquery JS Link -->
    <script src="{{ asset('website/assets') }}/js/jquery.min.js"></script>

    <!-- Bootstrap JS Link -->
    <script src="{{ asset('website/assets') }}/js/bootstrap.min.js"></script>
    <script src="{{ asset('website/assets') }}/js/popper.min.js"></script>

    <!-- Portfolio Tabbing JS Link -->
    <script src="{{ asset('website/assets') }}/js/jquery.mixitup.min.js"></script>

    <!-- Text Typing Js Link -->
    <script src="{{ asset('website/assets') }}/js/typed.min.js"></script>

    <!-- Slick Slider JS Link -->
    <script src="{{ asset('website/assets') }}/js/slick.min.js"></script>

    <!-- Wow Animation JS Link -->
    <script src="{{ asset('website/assets') }}/js/wow.min.js"></script>

    <!-- Magnific Popup -->
    <script src="{{ asset('website/assets') }}/vendor/magnific-popup/magnific-popup.js"></script>

    <!-- Custom JS Link -->
    <script src="{{ asset('website/assets') }}/js/dz.ajax.js"></script><!-- AJAX -->
    <script src="{{ asset('website/assets') }}/js/custom.js?{{ rand() }}"></script>
    <script src="{{ asset('website/assets') }}/js/custom-scroll-count.js"></script>

    <script src="{{asset('bootstrap-sweetalert')}}/sweetalert.min.js"></script>

    <script>

        (function($){

            @if ($message = Session::get('success'))

                swal({
                    title: " ",
                    text: "{{ $message }}",
                    imageUrl: '{{ asset('img/sent.jpg') }}'
                });

            @endif

            @if ($message = Session::get('error'))

                swal({
                    title: "",
                    text: "{{ $message }}",
                    type: "warning",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-danger",
                });

            @endif



        })(jQuery);

    </script>

    @yield('footer')

</body>
</html>
