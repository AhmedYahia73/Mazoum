<?php $lang = 'ar'; ?>

<!DOCTYPE html>
<html lang="<?php echo e($lang); ?>">
<head>
	<title>Mazoom</title>
	<meta charset="utf-8">
	<meta name="keywords" content="Mazoom" />
    <meta name="description" content="Mazoom" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  
  
  	<meta name="facebook-domain-verification" content="znwsakdp9gljom60umh4ysh621p4lx" />

	<!-- FavIcon Link -->

	<!-- Bootstrap CSS Link -->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('website/assets')); ?>/css/bootstrap.min.css">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

	<!-- Font Awesome Icon CSS Link -->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('website/assets')); ?>/css/font-awesome.min.css">

	<!-- Slick Slider CSS Link -->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('website/assets')); ?>/css/slick.css">
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('website/assets')); ?>/css/slick-theme.css">

	<!-- Magnific Popup -->
	<link href="<?php echo e(asset('website/assets')); ?>/vendor/magnific-popup/magnific-popup.min.css" rel="stylesheet">

	<!-- Wow Animation CSS Link -->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('website/assets')); ?>/css/animate.css">

	<!-- Main Style CSS Link -->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('website/assets')); ?>/css/style.css?<?php echo e(rand()); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('bootstrap-sweetalert')); ?>/sweetalert.css" />
    <link rel="stylesheet" href="<?php echo e(asset('arabic_font')); ?>/droidarabickufi.css"/>

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

    <?php echo $__env->yieldContent('header'); ?>

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
							<a href="<?php echo e(route($lang.'_home')); ?>" title="Mazoom">
								<img src="<?php echo e(asset('logo')); ?>/mazoom.png?<?php echo e(rand()); ?>" alt="Logo">
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
									<li class="<?php echo e(Route::is($lang.'_home') ? 'active' : ''); ?>">
                                        <a href="<?php echo e(route($lang.'_home')); ?>" title="Home">
                                            <?php echo e(trans('home.Home')); ?>

                                        </a>
                                    </li>

									<li class="<?php echo e(Route::is($lang.'-about-us') ? 'active' : ''); ?>">
                                        <a href="<?php echo e(route($lang.'-about-us')); ?>" title="About Us">
                                            <?php echo e(trans('home.About Us')); ?>

                                        </a>
                                    </li>

									<li class="<?php echo e(Route::is($lang.'_advantages') ? 'active' : ''); ?>">
                                        <a href="<?php echo e(route($lang.'_advantages')); ?>" title="Services">
                                            <?php echo e(trans('home.advantages')); ?>

                                        </a>
                                    </li>

									<li class="<?php echo e(Route::is($lang.'_portfolio') ? 'active' : ''); ?>">
                                        <a href="<?php echo e(route($lang.'_portfolio')); ?>" title="Services">
                                            <?php echo e(trans('home.Desgins')); ?>

                                        </a>
                                    </li>

									<li class="<?php echo e(Route::is($lang.'_pricing') ? 'active' : ''); ?>">
                                        <a href="<?php echo e(route($lang.'_pricing')); ?>" title="Services">
                                            <?php echo e(trans('home.Pricing')); ?>

                                        </a>
                                    </li>

									<li class="<?php echo e(Route::is($lang.'_view_contact_us') ? 'active' : ''); ?>">
                                        <a href="<?php echo e(route($lang.'_view_contact_us')); ?>" title="Contact Us">
                                            <?php echo e(trans('home.Contact Us')); ?>

                                        </a>
                                    </li>
								</ul>
							</nav>
							<div class="black-shadow"></div>
						</div>

						<div class="header-search">
							<a href="<?php echo e(route($lang.'_view_contact_us')); ?>" class="sec-btn" title="<?php echo e(trans('home.Get Started')); ?>">
                                <span>
                                    <?php echo e(trans('home.Get Started')); ?>

                                </span>
                            </a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header End -->


    <?php echo $__env->yieldContent('content'); ?>


	<!-- Footer CSS Start -->
	<footer class="site-footer back-img" style="background-image: url('<?php echo e(asset('logo/footer.jpg')); ?>');">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<div class="newsletter">
						<h2 class="h2-title" style="font-size: 35px;"> <?php echo e($lang == 'en' ? 'Subscribe To Our Newsletter For Latest Update' : 'اشترك في النشرة الإخبارية لدينا للحصول على آخر التحديثات'); ?> </h2>
						<div class="newsletter-form">

							<form class="contact-form" action="<?php echo e(route($lang.'-save-subscribe')); ?>" id="save-subscriber" method="post">

                                <?php echo e(csrf_field()); ?>


                                <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e($lang == 'en' ? 'Email Address' : 'البريد الألكتروني'); ?>" required>
								<button form="save-subscriber" value="Submit" type="submit" class="sec-btn"><span> <?php echo e($lang == 'en' ? 'Subscribe Now' : 'أشترك الأن'); ?> </span></button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="row align-items-center">
				<div class="col-lg-4">
					<div class="footer-logo">
						<a href="<?php echo e(route($lang.'_home')); ?>" title="Mazoom" style="color: #FFF;width: auto;">
                      		معزوم الكويت للدعوات الالكتروانيه
                        </a>
					</div>
				</div>
				<div class="col-lg-8">
					<div class="footer-menu">
						<ul>
							<li><a href="<?php echo e(route($lang.'_home')); ?>" title="<?php echo e(trans('home.Home')); ?>"> <?php echo e(trans('home.Home')); ?> </a></li>
							<li><a href="<?php echo e(route($lang.'-about-us')); ?>" title="<?php echo e(trans('home.About Us')); ?>"> <?php echo e(trans('home.About Us')); ?> </a></li>
							<li><a href="<?php echo e(route($lang.'_advantages')); ?>" title="<?php echo e(trans('home.advantages')); ?>"> <?php echo e(trans('home.advantages')); ?> </a></li>
							<li><a href="<?php echo e(route($lang.'_portfolio')); ?>" title="<?php echo e(trans('home.Desgins')); ?>"> <?php echo e(trans('home.Desgins')); ?> </a></li>
							<li><a href="<?php echo e(route($lang.'_pricing')); ?>" title="<?php echo e(trans('home.Pricing')); ?>"> <?php echo e(trans('home.Pricing')); ?> </a></li>
							<li><a href="<?php echo e(route($lang.'_view_contact_us')); ?>" title="<?php echo e(trans('home.Contact Us')); ?>"> <?php echo e(trans('home.Contact Us')); ?> </a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="footer-bottom">
				<div class="row align-items-center">
					<div class="col-lg-8 order-lg-1 order-2">
						<div class="copy-right">
							<p>
                                Copyright © <?php echo e(Date('Y')); ?>

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
    <script src="<?php echo e(asset('website/assets')); ?>/js/jquery.min.js"></script>

    <!-- Bootstrap JS Link -->
    <script src="<?php echo e(asset('website/assets')); ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo e(asset('website/assets')); ?>/js/popper.min.js"></script>

    <!-- Portfolio Tabbing JS Link -->
    <script src="<?php echo e(asset('website/assets')); ?>/js/jquery.mixitup.min.js"></script>

    <!-- Text Typing Js Link -->
    <script src="<?php echo e(asset('website/assets')); ?>/js/typed.min.js"></script>

    <!-- Slick Slider JS Link -->
    <script src="<?php echo e(asset('website/assets')); ?>/js/slick.min.js"></script>

    <!-- Wow Animation JS Link -->
    <script src="<?php echo e(asset('website/assets')); ?>/js/wow.min.js"></script>

    <!-- Magnific Popup -->
    <script src="<?php echo e(asset('website/assets')); ?>/vendor/magnific-popup/magnific-popup.js"></script>

    <!-- Custom JS Link -->
    <script src="<?php echo e(asset('website/assets')); ?>/js/dz.ajax.js"></script><!-- AJAX -->
    <script src="<?php echo e(asset('website/assets')); ?>/js/custom.js?<?php echo e(rand()); ?>"></script>
    <script src="<?php echo e(asset('website/assets')); ?>/js/custom-scroll-count.js"></script>

    <script src="<?php echo e(asset('bootstrap-sweetalert')); ?>/sweetalert.min.js"></script>

    <script>

        (function($){

            <?php if($message = Session::get('success')): ?>

                swal({
                    title: " ",
                    text: "<?php echo e($message); ?>",
                    imageUrl: '<?php echo e(asset('img/sent.jpg')); ?>'
                });

            <?php endif; ?>

            <?php if($message = Session::get('error')): ?>

                swal({
                    title: "",
                    text: "<?php echo e($message); ?>",
                    type: "warning",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-danger",
                });

            <?php endif; ?>



        })(jQuery);

    </script>

    <?php echo $__env->yieldContent('footer'); ?>

</body>
</html>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/layouts/master.blade.php ENDPATH**/ ?>