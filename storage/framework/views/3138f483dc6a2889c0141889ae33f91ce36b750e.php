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
	<section class="main-banner inner-banner back-img" id="main-banner" style="background-image: url('<?php echo e(asset('logo/pricing.jpg')); ?>');">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="banner-content"  style="display:none">
						<h1 class="h1-title wow fadeup-animation" data-wow-delay="0.2s"> <?php echo e(trans('home.Pricing')); ?> </h1>
						<div class="breadcrumb-box wow fadeup-animation" data-wow-delay="0.4s">
							<ul>
								<li><a href="<?php echo e(route($lang.'_home')); ?>" title="Home"> <?php echo e(trans('home.Home')); ?> </a></li>
								<li><?php echo e(trans('home.Pricing')); ?></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Banner End -->

    <?php
        $Pricing = App\Models\Pricing::get();
    ?>

	<!-- Our Pricing Start -->
	<section class="main-pricing page-pricing" style="padding-bottom: 100px">
		<div class="container">

            <?php echo $__env->make('user.includes.pricing', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		</div>
	</section>
	<!-- Our Pricing End -->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/pages/pricing.blade.php ENDPATH**/ ?>