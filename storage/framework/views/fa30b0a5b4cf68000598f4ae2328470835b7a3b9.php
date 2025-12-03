<?php $__env->startSection('title','أضافة حدث جديد'); ?>

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

    .hasTime .flatpickr-innerContainer {
        display: none !important
    }

    .hasTime .flatpickr-months {
        display: none !important
    }

    .light-style .flatpickr-calendar.hasTime .flatpickr-time {
        direction: ltr;
    }

  </style>

  <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/flatpickr/flatpickr.css" />
  <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/pickr/pickr-themes.css" />

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
            أضافة حدث جديد
          </h4>
        </div>


        <?php echo Form::open(['url' => "admin/events", 'role'=>'form','id'=>'add','method'=>'post', 'files' => true]); ?>


            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 col-sm-6 <?php echo e($errors->has('assistant_id') ? ' has-error' : ''); ?>">
                        <label> الموظفين  <span class="text-danger">*</span> </label>
                        <select name="assistant_id" id="assistant_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true">  الموظفين  </option>
                            <?php $__currentLoopData = Assistants(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php if(old('assistant_id') == $key): ?> <?php echo e('selected'); ?> <?php endif; ?>> <?php echo e($value); ?> </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($errors->has('assistant_id')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('assistant_id')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 col-sm-6 <?php echo e($errors->has('country_code') ? ' has-error' : ''); ?>">
                        <label> الدوله  <span class="text-danger">*</span> </label>
                        <select name="country_code" id="country_code" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true">  الدوله  </option>
                            <option value="kw" <?php if(old('country_code') == 'kw'): ?> <?php echo e('selected'); ?> <?php endif; ?>> الكويت </option>
                            <option value="sa" <?php if(old('country_code') == 'sa'): ?> <?php echo e('selected'); ?> <?php endif; ?>> السعودية </option>
                        </select>
                        <?php if($errors->has('country_code')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('country_code')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('title') ? ' has-error' : ''); ?>">
                        <label> عنوان الحدث </label>
                        <input type="text" name="title" required class="form-control m-input" value="<?php echo e(old('title')); ?>"  placeholder="عنوان الحدث ">
                        <?php if($errors->has('title')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('title')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 col-sm-6 <?php echo e($errors->has('user_id') ? ' has-error' : ''); ?>">
                        <label> المستخدمين  <span class="text-danger">*</span> </label>
                        <select name="user_id" id="user_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true">  المستخدمين  </option>
                            <?php $__currentLoopData = Users(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php if(old('user_id') == $key): ?> <?php echo e('selected'); ?> <?php endif; ?>> <?php echo e($value); ?> </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($errors->has('user_id')): ?>
                         <span class="help-block" style="color:red">
                             <strong><?php echo e($errors->first('user_id')); ?></strong>
                         </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('address') ? ' has-error' : ''); ?>">
                        <label> موقع الحدث </label>
                        <input type="text" name="address" required class="form-control m-input" value="<?php echo e(old('address')); ?>"  placeholder="موقع الحدث ">
                        <?php if($errors->has('address')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('address')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6 <?php echo e($errors->has('showing_qr') ? ' has-error' : ''); ?>">
                        <label>  هل تريد أظهار ال QR <span class="text-danger">*</span> </label>
                        <select name="showing_qr" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="yes" <?php if(old('showing_qr') == 'yes'): ?> <?php echo e('selected'); ?> <?php endif; ?>> نعم </option>
                            <option value="no"  <?php if(old('showing_qr') == 'no'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> لا </option>
                        </select>
                        <?php if($errors->has('showing_qr')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('showing_qr')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('have_reminder') ? ' has-error' : ''); ?>">
                        <label>  هل تريد تفعيل رسائل التذكير <span class="text-danger">*</span> </label>
                        <select name="have_reminder" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="yes" <?php if(old('have_reminder') == 'yes'): ?> <?php echo e('selected'); ?> <?php endif; ?>> نعم </option>
                            <option value="no"  <?php if(old('have_reminder') == 'no'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> لا </option>
                        </select>
                        <?php if($errors->has('have_reminder')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('have_reminder')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('can_replay_messages') ? ' has-error' : ''); ?>">
                        <label>  هل تريد تفعيل الرد علي الرسائل <span class="text-danger">*</span> </label>
                        <select name="can_replay_messages" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="yes" <?php if(old('can_replay_messages') == 'yes'): ?> <?php echo e('selected'); ?> <?php endif; ?>> نعم </option>
                            <option value="no"  <?php if(old('can_replay_messages') == 'no'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> لا </option>
                        </select>
                        <?php if($errors->has('can_replay_messages')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('can_replay_messages')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('enable_resend_again') ? ' has-error' : ''); ?>">
                        <label>  هل تريد تفعيل اعاده ارسال <span class="text-danger">*</span> </label>
                        <select name="enable_resend_again" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="yes" <?php if(old('enable_resend_again') == 'yes'): ?> <?php echo e('selected'); ?> <?php endif; ?>> نعم </option>
                            <option value="no"  <?php if(old('enable_resend_again') == 'no'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> لا </option>
                        </select>
                        <?php if($errors->has('enable_resend_again')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('enable_resend_again')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('sending_type') ? ' has-error' : ''); ?>">
                        <label>  نوع الأرسال <span class="text-danger">*</span> </label>
                        <select name="sending_type" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="old_send"      <?php if(old('sending_type') == 'old_send'): ?> <?php echo e('selected'); ?> <?php endif; ?>>  أرسال ميتا  </option>
                            <option value="new_send"      <?php if(old('sending_type') == 'new_send'): ?>  <?php echo e('selected'); ?> <?php endif; ?>>أرسال </option>
                            <option value="not_available" <?php if(old('sending_type') == 'not_available'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> غير متاح </option>
                        </select>
                        <?php if($errors->has('sending_type')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('sending_type')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('lat') ? ' has-error' : ''); ?>">
                        <label> دوائر العرض </label>
                        <input type="text" name="lat" class="form-control m-input" value="0"  placeholder="دوائر العرض ">
                        <?php if($errors->has('lat')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('lat')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('long') ? ' has-error' : ''); ?>">
                        <label> خطوط الطول </label>
                        <input type="text" name="long" class="form-control m-input" value="0"  placeholder="خطوط الطول ">
                        <?php if($errors->has('long')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('long')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>



                    <div class="col-sm-6 <?php echo e($errors->has('date') ? ' has-error' : ''); ?>">
                        <label> تاريخ الحدث <span class="text-danger">*</span> </label>
                        <input type="text" name="date" required class="form-control m-input" value="<?php echo e(old('date')); ?>"  placeholder="YYYY-MM-DD" id="flatpickr-date">
                        <?php if($errors->has('date')): ?>
                             <span class="help-block" style="color:red">
                                  <strong><?php echo e($errors->first('date')); ?> </strong>
                             </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6 <?php echo e($errors->has('time') ? ' has-error' : ''); ?>">
                        <label> وقت الحدث <span class="text-danger">*</span> </label>
                        <input type="text" name="time" id="flatpickr-date3" required class="form-control m-input" value="<?php echo e(old('time')); ?>"  placeholder="وقت الحدث">
                        <?php if($errors->has('date')): ?>
                             <span class="help-block" style="color:red">
                                  <strong><?php echo e($errors->first('time')); ?> </strong>
                             </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-12 col-sm-12 <?php echo e($errors->has('color') ? ' has-error' : ''); ?>">
                        <label> اللون  </label>
                        <input type="color" name="color" required class="form-control m-input" value="<?php echo e(old('color')); ?>"  placeholder=" اللون ">
                        <?php if($errors->has('color')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('color')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>


                    <div class="col-sm-6 <?php echo e($errors->has('file') ? ' has-error' : ''); ?>">
                        <label> تصميم الدعوه <span class="text-danger">*</span> </label>
                        <input class="form-control" type="file" id="formFile" required name="file" />
                        <?php if($errors->has('file')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('file')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6 <?php echo e($errors->has('image') ? ' has-error' : ''); ?>">
                        <label> تصميم QR   </label>
                        <input class="form-control" type="file" id="formFile" name="image" />
                        <?php if($errors->has('image')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('image')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-12 <?php echo e($errors->has('video') ? ' has-error' : ''); ?>">
                        <label> video  </label>
                        <input class="form-control" type="file" id="formVideo" name="video" accept="video/*" />
                        <?php if($errors->has('video')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('video')); ?></strong>
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



<?php $__env->startSection('footer'); ?>


    <!-- Vendors JS -->
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/pickr/pickr.js"></script>

    <!-- Page JS -->
    <script>
        'use strict';

        (function () {

            // Flat Picker
            // --------------------------------------------------------------------
            const  flatpickrDateTime = document.querySelector('#flatpickr-date');

            // Datetime
            if (flatpickrDateTime) {
                flatpickrDateTime.flatpickr({
                    enableTime: false,
                    dateFormat: 'Y-m-d'
                });
            }

        })();

    </script>


<?php $__env->stopSection(); ?>











<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/events/create.blade.php ENDPATH**/ ?>