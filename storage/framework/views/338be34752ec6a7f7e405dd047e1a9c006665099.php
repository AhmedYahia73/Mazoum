<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>404 page</title>

	<!-- Google font -->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('user')); ?>/dist/css/lato_font.css" />

	<!-- Font Awesome Icon -->
	<link type="text/css" rel="stylesheet" href="<?php echo e(url('errors')); ?>/css/font-awesome.min.css" />

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="<?php echo e(url('errors')); ?>/css/style.css" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="<?php echo e(url('errors')); ?>/js/html5shiv.min.js"></script>
		  <script src="<?php echo e(url('errors')); ?>/js/respond.min.js"></script>
        <![endif]-->
        
    <style>
        body, html, h1, h2, h3, h4, h5, h6, p, a, li {
            font-family: 'Lato', sans-serif !important;
        }
    </style>

</head>

<body>

    <?php $setting = App\Models\Setting::first(); ?>

	<div id="notfound">
		<div class="notfound-bg"></div>
		<div class="notfound">
			<div class="notfound-404">
				<h1>404</h1>
			</div>
			<h2 style="text-transform: lowercase;">we are sorry, but the page you requested was not found</h2>
			
            <a href="<?php echo e(url('/')); ?>" class="home-btn">Go Home</a>
            
			<a href="<?php echo e(url('contact_us')); ?>" class="contact-btn">Contact us</a>
            
			<div class="notfound-social">
				<a href="<?php echo e($setting->facebook_link); ?>"><i class="fa fa-facebook"></i></a>
				<a href="<?php echo e($setting->twitter_link); ?>"><i class="fa fa-twitter"></i></a>
				<a href="<?php echo e($setting->instgram_link); ?>"><i class="fa fa-instagram"></i></a>
			</div>
		</div>
	</div>

</body>

</html>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/errors/404.blade.php ENDPATH**/ ?>