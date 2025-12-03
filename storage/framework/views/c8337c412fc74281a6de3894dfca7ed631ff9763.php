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
    <section class="main-banner inner-banner back-img" id="main-banner" style="background-image: url('<?php echo e(asset('logo/desgins.jpg')); ?>');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="banner-content"  style="display:none">
                        <h1 class="h1-title wow fadeup-animation" data-wow-delay="0.2s"> <?php echo e(trans('home.Desgins')); ?> </h1>
                        <div class="breadcrumb-box wow fadeup-animation" data-wow-delay="0.4s">
                            <ul>
                                <li><a href="<?php echo e(route($lang.'_home')); ?>" title="Home"> <?php echo e(trans('home.Home')); ?> </a></li>
                                <li><?php echo e(trans('home.Desgins')); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner End -->

    <?php
        $Desgins = App\Models\WebDesgins::get();
    ?>

    <?php if($Desgins != null && $Desgins->count() > 0): ?>
	<!-- Portfolio Tabbing Start -->
	<div class="main-portfolio-tabbing">
		<div class="container">

            <?php echo $__env->make('user.includes.desgins', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		</div>
	</div>
	<!-- Portfolio Tabbing End -->
    <?php endif; ?>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/pages/portfolio.blade.php ENDPATH**/ ?>