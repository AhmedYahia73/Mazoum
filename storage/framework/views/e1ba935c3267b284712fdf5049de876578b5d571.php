
<?php echo Form::model($Item, [ 'route' => ['admin.users.update' , $Item->id ] , 'method' => 'patch', 'files'=>true, 'role'=>'form','id'=>'edit', 'class'=> 'm-form m-form--fit m-form--label-align-left' ]); ?>


    <?php echo e(csrf_field()); ?>


    <input type="hidden" name="id" value="<?php echo e($Item->id); ?>">

    <div class="card-body">
        <div class="row" style="margin-top: 30px">


            <div class="col-lg-12 col-sm-12 <?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                <label>  <?php echo e(trans('home.name')); ?>  <span class="text-danger">*</span>  </label>
                <input type="text" name="name" class="form-control m-input" required="required" value="<?php echo e($Item->name); ?>" placeholder="  <?php echo e(trans('home.name')); ?>   ">
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
                <select name="mobile_code" class="form-control m-bootstrap-select m_selectpicker"  data-live-search="true" required>
                    <option value="" disabled selected>  أختر كود </option>
                    <?php if($mobile_codes != null && $mobile_codes->count() > 0): ?>
                        <?php $__currentLoopData = $mobile_codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value->code); ?>" <?php if($Item->mobile_code == $value->code): ?> selected <?php endif; ?>> <?php echo e($value->ar_country_name); ?> (<?php echo e($value->code); ?>) </option>
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
                <input type="text" name="mobile" class="form-control m-input" required="required" value="<?php echo e($Item->mobile); ?>" placeholder=" <?php echo e(trans('home.mobile')); ?> ">
                <?php if($errors->has('mobile')): ?>
                    <span class="help-block" style="color:red">
                        <strong><?php echo e($errors->first('mobile')); ?> </strong>
                    </span>
                <?php endif; ?>
            </div>
          
            <div class="col-lg-12 <?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
              <label>  <?php echo e(trans('home.password')); ?>   </label>
              <input type="text" name="password" class="form-control m-input" value="<?php echo e($Item->pass); ?>" placeholder="  <?php echo e(trans('home.password')); ?>  ">
              <?php if($errors->has('password')): ?>
              <span class="help-block" style="color:red">
                <strong><?php echo e($errors->first('password')); ?> </strong>
              </span>
              <?php endif; ?>
            </div>


            <div class="col-lg-12" style="margin-bottom:20px">
                <button type="submit" form="edit" class="btn btn-success" style="margin-top:20px;margin-bottom: 0px;"><?php echo e(trans('home.update')); ?></button>
            </div>

        </div>
    </div>


<?php echo Form::close(); ?>

<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/users/edit_form.blade.php ENDPATH**/ ?>