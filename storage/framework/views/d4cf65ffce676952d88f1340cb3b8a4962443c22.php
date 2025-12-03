<?php $__env->startSection('title',trans('home.home')); ?>

<?php $__env->startSection('content'); ?>

<div class="row">

    <!-- Website Analytics-->
    <div class="col-lg-12 col-md-12 mb-4">

        <h3>
            مرحبا بك يا  <?php echo e(auth()->guard('admin')->user()->name); ?>

        </h3>

    </div>

  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/layouts/home.blade.php ENDPATH**/ ?>