<?php $__env->startSection('title',trans('home.add_user')); ?>

<?php $__env->startSection('header'); ?>

  <style>

    .card-body .col-sm-12 , .card-body .col-sm-4 , .card-body .col-sm-6 { margin-bottom: 20px }

    .card-body { padding-bottom: 0 !important}

    .card-footer {
      padding-top: 10px !important;
      padding-left: 40px !important;
      margin-top: 0 !important;
      padding-bottom: 30px !important;
    }

    .un_active {
      display: none
    }

    .active {
      display: block
    }

  </style>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>

<!-- Basic Inputs start -->
<section id="basic-input">

  <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            <?php echo e(trans('home.add_user')); ?>

          </h4>
        </div>


        <?php echo Form::open(['url' => "admin/users", 'role'=>'form','id'=>'add','method'=>'post']); ?>


            <div class="card-body">

                <div class="row">

                    <div class="col-lg-12 col-sm-12 <?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                        <label>  <?php echo e(trans('home.name')); ?>  <span class="text-danger">*</span>  </label>
                        <input type="text" name="name" class="form-control m-input" required="required" value="<?php echo e(old('name')); ?>" placeholder="  <?php echo e(trans('home.name')); ?>   ">
                        <?php if($errors->has('name')): ?>
                             <span class="help-block" style="color:red">
                                  <strong><?php echo e($errors->first('name')); ?> </strong>
                             </span>
                        <?php endif; ?>
                    </div>

                    <?php
                        $mobile_codes = App\Models\MobileCodes::get(['id','ar_country_name','code']);
                    ?>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('mobile_code') ? ' has-error' : ''); ?>">
                        <label>  كود الموبيل <span class="text-danger">*</span>   </label>
                        <select name="mobile_code" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" disabled selected>  أختر كود </option>
                            <?php if($mobile_codes != null && $mobile_codes->count() > 0): ?>
                                <?php $__currentLoopData = $mobile_codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value->code); ?>" <?php if(old('mobile_code') == $value->code): ?> selected <?php endif; ?>> <?php echo e($value->ar_country_name); ?> (<?php echo e($value->code); ?>) </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                        <?php if($errors->has('mobile_code')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('mobile_code')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>



                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('mobile') ? ' has-error' : ''); ?>">
                        <label>  <?php echo e(trans('home.mobile')); ?> <span class="text-danger">*</span>   </label>
                        <input type="text" name="mobile" class="form-control m-input" required="required" value="<?php echo e(old('mobile')); ?>" placeholder=" <?php echo e(trans('home.mobile')); ?> ">
                        <?php if($errors->has('mobile')): ?>
                             <span class="help-block" style="color:red">
                                  <strong><?php echo e($errors->first('mobile')); ?> </strong>
                             </span>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

            <div class="card-footer" style="padding-left: 25px !important;margin-top: 0px !important;">
              <button type="submit" form="add" class="btn btn-primary mr-2">
                <?php echo e(trans('home.save')); ?>

              </button>
            </div>

        <?php echo Form::close(); ?>

         <!--end::Form-->


      </div>
    </div>
  </div>
</section>
<!-- Basic Inputs end -->



<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/users/create.blade.php ENDPATH**/ ?>