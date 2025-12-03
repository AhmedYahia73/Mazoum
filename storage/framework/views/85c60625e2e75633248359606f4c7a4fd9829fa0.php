<?php
    $lang = app()->getLocale();
?>




<?php $__env->startSection('header'); ?>

	<style>
    	.contact-page-link-text {
          width: calc(100% - 10px);
          padding-right: 10px;
      }  
	</style>

	<style>
      .main-banner.inner-banner:before , .site-branding a{
      	display:none
      }  
	</style>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>


    <!-- Banner Start -->
    <section class="main-banner inner-banner back-img" id="main-banner" style="background-image: url('<?php echo e(asset('logo/contact.jpg')); ?>');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="banner-content"  style="display:none">
                        <h1 class="h1-title wow fadeup-animation" data-wow-delay="0.2s"> <?php echo e(trans('home.Contact Us')); ?> </h1>
                        <div class="breadcrumb-box wow fadeup-animation" data-wow-delay="0.4s">
                            <ul>
                                <li><a href="<?php echo e(route($lang.'_home')); ?>" title="Home"> <?php echo e(trans('home.Home')); ?> </a></li>
                                <li><?php echo e(trans('home.Contact Us')); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner End -->

	<!-- Get In Touch Start -->
	<section class="main-contact page-contact">
		<div class="container">
			<div class="row align-items-center" id="contact">

				<div class="col-xl-6">
					<div class="contact-text wow right-animation" data-wow-delay="0.4s">
						<span class="sub-title"> تواصل معنا </span>
						<h2 class="h2-title" style="font-size: 40px;"> الحصول على اتصال معنا الآن ! </h2>
						<div class="contact-page-link">
							<div class="contact-page-link-box">
								<div class="contact-page-link-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/call-icon.png" alt="Call Icon">
								</div>
								<div class="contact-page-link-text">
									<h3 class="h3-title"> اتصل الان </h3>
									<p><a href="tel:96597378181" title="Call on +96597378181">+96597378181</a></p>
								</div>
							</div>
							<div class="contact-page-link-box">
								<div class="contact-page-link-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/mail-icon.png" alt="Mail Icon">
								</div>
								<div class="contact-page-link-text">
									<h3 class="h3-title"> البريد الإلكتروني </h3>
									<p><a href="mailto:admin@mazoom-kw.com" title="Mail on admin@mazoom-kw.com"> admin@mazoom-kw.com </a></p>
								</div>
							</div>
							
						</div>
					</div>
				</div>

				<div class="col-xl-6">
					<?php echo $__env->make('user.includes.contact', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				</div>

			</div>
		</div>
	</section>
	<!-- Get In Touch End -->

	<!-- Contact Map Start -->
	<div class="contact-map" style="display:none">
		<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7016.328264634826!2d77.02397704760743!3d28.444468247121968!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d183e9439f8d1%3A0x8feee7d4d8275f66!2sCommissionerate%20Of%20Police!5e0!3m2!1sen!2sin!4v1618593106816!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
	</div>
	<!-- Contact Map End -->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/pages/contact-us.blade.php ENDPATH**/ ?>