<?php
    $lang = app()->getLocale();
?>




<?php $__env->startSection('header'); ?>
	<style>
      .main-banner.inner-banner:before , .site-branding a{
      	display:none
      }  
	</style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>


<!-- Banner Start -->
	<section class="main-banner inner-banner back-img" id="main-banner" style="background-image: url('<?php echo e(asset('logo/services.jpg')); ?>');">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="banner-content"  style="display:none">
						<h1 class="h1-title wow fadeup-animation" data-wow-delay="0.2s"> <?php echo e(trans('home.advantages')); ?> </h1>
                        <div class="breadcrumb-box wow fadeup-animation" data-wow-delay="0.4s">
                            <ul>
                                <li><a href="<?php echo e(route($lang.'_home')); ?>" title="Home"> <?php echo e(trans('home.Home')); ?> </a></li>
                                <li><?php echo e(trans('home.advantages')); ?></li>
                            </ul>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Banner End -->

	<!-- Services Start -->
	<section class="main-services page-services-list" style="padding-bottom: 100px">
		<div class="container">
			<div class="services-list">
				<div class="row justify-content-center">
					<div class="col-xl-4 col-lg-4 col-md-6">
						<div class="service-box wow fadeup-animation" data-wow-delay="0.2s">
							<div class="service-box-text">
								<div class="service-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/web-design-icon.png" alt="Web Design">
								</div>
								<h3 class="h3-title"> تصميم مبتكر وسهل  </h3>
								<p>
                                  يوفر التطبيق واجهة بسيطة ومبتكرة تتيح للمستخدمين إنشاء بطاقات دعوة إلكترونية بشكل فعّال وبسهولة، حيث يمكنهم استخدام مجموعة متنوعة من الأدوات والتصاميم لتخصيص دعواتهم.
                                </p>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-lg-4 col-md-6">
						<div class="service-box idea-research-service wow fadeup-animation" data-wow-delay="0.3s">
							<div class="service-box-text">
								<div class="service-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/idea-research-icon.png" alt="Idea & Research">
								</div>
								<h3 class="h3-title"> تجربة تفاعلية </h3>
								<p>
									يقدم التطبيق تجربة تفاعلية تتيح للمستخدمين استعراض ومشاركة بطاقاتهم بشكل سلس وجذاب، مع ميزات إضافية مثل الردود التلقائية والمشاركة عبر وسائل التواصل الاجتماعي.
                                </p>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-lg-4 col-md-6">
						<div class="service-box seo-service wow fadeup-animation" data-wow-delay="0.4s">
							<div class="service-box-text">
								<div class="service-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/seo-icon.png" alt="SEO & Marketing">
								</div>
								<h3 class="h3-title"> تنوع في القوالب والتصاميم </h3>
								<p>
									يتيح التطبيق الوصول إلى مجموعة واسعة من القوالب والتصاميم المصممة بعناية لتناسب جميع المناسبات، مما يسهل على المستخدمين اختيار الستايل الذي يناسب ذوقهم.
                                </p>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-lg-4 col-md-6">
						<div class="service-box development-service wow fadeup-animation" data-wow-delay="0.5s">
							<div class="service-box-text">
								<div class="service-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/web-development-icon.png" alt="Web Development">
								</div>
								<h3 class="h3-title"> تقنيات متقدمة </h3>
								<p>
									يعتمد التطبيق على أحدث التقنيات لضمان أمان البيانات واستقرار الأداء، مع دعم لمختلف الأجهزة والمنصات.
                                </p>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-lg-4 col-md-6">
						<div class="service-box time-data-service wow fadeup-animation" data-wow-delay="0.6s">
							<div class="service-box-text">
								<div class="service-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/time-data-icon.png" alt="Real Time & Data">
								</div>
								<h3 class="h3-title"> احترافية في الأداء </h3>
								<p>
									نحن ملتزمون بتقديم خدمة عالية الجودة، مع فريق دعم فني متاح لمساعدة المستخدمين في حال وجود أي استفسارات أو مشكلات.
                                </p>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-lg-4 col-md-6">
						<div class="service-box analysis-service wow fadeup-animation" data-wow-delay="0.7s">
							<div class="service-box-text">
								<div class="service-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/analysis-icon.png" alt="Analysis">
								</div>
								<h3 class="h3-title"> استدامة وبيئة ودية </h3>
								<p>	
									نسعى لتعزيز الوعي بالبيئة من خلال استخدام تكنولوجيا صديقة للبيئة وتشجيع المستخدمين على التحول إلى الدعوات الرقمية وتقليل استهلاك الورق.
                                </p>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</section>
	<!-- Services End -->



<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/pages/services.blade.php ENDPATH**/ ?>