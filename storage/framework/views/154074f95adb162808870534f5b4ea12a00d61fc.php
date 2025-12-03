
<!--begin::Form-->
<?php echo Form::model($Item, [ 'route' => ['admin.events.update' , $Item->id ] , 'method' => 'patch', 'role'=>'form','id'=>'edit', 'files' => true ]); ?>


    <input type="hidden" name="id" value="<?php echo e($Item->id); ?>">

    <div class="row">

        <div class="col-md-6 col-sm-6 <?php echo e($errors->has('assistant_id') ? ' has-error' : ''); ?>">
            <label> الموظفين </label>
            <select name="assistant_id" id="assistant_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
                <option value="" selected="true">  الموظفين  </option>
                <?php $__currentLoopData = Assistants(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php if($Item->assistant_id == $key): ?> <?php echo e('selected'); ?> <?php endif; ?>> <?php echo e($value); ?> </option>
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
                <option value="kw" <?php if($Item->country_code == 'kw'): ?> <?php echo e('selected'); ?> <?php endif; ?>> الكويت </option>
                <option value="sa" <?php if($Item->country_code == 'sa'): ?> <?php echo e('selected'); ?> <?php endif; ?>> السعودية </option>
            </select>
            <?php if($errors->has('country_code')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('country_code')); ?></strong>
                </span>
            <?php endif; ?>
        </div>



        <div class="col-lg-12 col-sm-12 <?php echo e($errors->has('title') ? ' has-error' : ''); ?>">
            <label> عنوان الحدث </label>
            <input type="text" name="title" required class="form-control m-input" value="<?php echo e($Item->title); ?>"  placeholder="عنوان الحدث ">
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
                    <option value="<?php echo e($key); ?>" <?php if($Item->user_id == $key): ?> <?php echo e('selected'); ?> <?php endif; ?>> <?php echo e($value); ?> </option>
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
            <input type="text" name="address" required class="form-control m-input" value="<?php echo e($Item->address); ?>"  placeholder="موقع الحدث ">
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
                <option value="yes" <?php if($Item->showing_qr == 'yes'): ?> <?php echo e('selected'); ?> <?php endif; ?>> نعم </option>
                <option value="no"  <?php if($Item->showing_qr == 'no'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> لا </option>
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
                <option value="yes" <?php if($Item->have_reminder == 'yes'): ?> <?php echo e('selected'); ?> <?php endif; ?>> نعم </option>
                <option value="no"  <?php if($Item->have_reminder == 'no'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> لا </option>
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
                <option value="yes" <?php if($Item->can_replay_messages == 'yes'): ?> <?php echo e('selected'); ?> <?php endif; ?>> نعم </option>
                <option value="no"  <?php if($Item->can_replay_messages == 'no'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> لا </option>
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
                <option value="yes" <?php if($Item->enable_resend_again == 'yes'): ?> <?php echo e('selected'); ?> <?php endif; ?>> نعم </option>
                <option value="no"  <?php if($Item->enable_resend_again == 'no'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> لا </option>
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
                <option value="old_send"      <?php if($Item->sending_type == 'old_send'): ?> <?php echo e('selected'); ?> <?php endif; ?>>  أرسال ميتا  </option>
                <option value="new_send"      <?php if($Item->sending_type == 'new_send'): ?>  <?php echo e('selected'); ?> <?php endif; ?>>أرسال </option>
                <option value="not_available" <?php if($Item->sending_type == 'not_available'): ?>  <?php echo e('selected'); ?> <?php endif; ?>> غير متاح </option>
            </select>
            <?php if($errors->has('sending_type')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('sending_type')); ?> </strong>
                </span>
            <?php endif; ?>
        </div>

        <div class="col-lg-6 col-md-6 <?php echo e($errors->has('gender') ? ' has-error' : ''); ?>">
            <label> الجنس <span class="text-danger">*</span>   </label>
            <select name="gender" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
              <option value="" disabled selected>  أختر رجل / أمرأه </option>
              <option value="male" <?php if($Item->gender == 'male'): ?> selected <?php endif; ?>> رجل </option>
              <option value="female" <?php if($Item->gender == 'female'): ?> selected <?php endif; ?>> أمرأه </option>
            </select>
            <?php if($errors->has('gender')): ?>
            <span class="help-block" style="color:red">
              <strong><?php echo e($errors->first('gender')); ?> </strong>
            </span>
            <?php endif; ?>
        </div>

        <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('long') ? ' has-error' : ''); ?>">
            <label> خطوط الطول </label>
            <input type="text" name="long" class="form-control m-input" value="<?php echo e($Item->long); ?>"  placeholder="خطوط الطول ">
            <?php if($errors->has('long')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('long')); ?></strong>
                </span>
            <?php endif; ?>
        </div>


        <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('lat') ? ' has-error' : ''); ?>">
            <label> دوائر العرض </label>
            <input type="text" name="lat" class="form-control m-input" value="<?php echo e($Item->lat); ?>"  placeholder="دوائر العرض ">
            <?php if($errors->has('lat')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('lat')); ?></strong>
                </span>
            <?php endif; ?>
        </div>

    

        <div class="col-sm-6 <?php echo e($errors->has('date') ? ' has-error' : ''); ?>">
            <label> تاريخ الحدث <span class="text-danger">*</span> </label>
            <input type="text" name="date" required class="form-control m-input" value="<?php echo e($Item->date); ?>"  placeholder="YYYY-MM-DD" id="flatpickr-date">
            <?php if($errors->has('date')): ?>
                 <span class="help-block" style="color:red">
                      <strong><?php echo e($errors->first('date')); ?> </strong>
                 </span>
            <?php endif; ?>
        </div>

        <div class="col-sm-6 <?php echo e($errors->has('time') ? ' has-error' : ''); ?>">
            <label> وقت الحدث <span class="text-danger">*</span> </label>
            <input type="text" name="time" id="flatpickr-date3" required class="form-control m-input" value="<?php echo e($Item->time); ?>"  placeholder="وقت الحدث">
            <?php if($errors->has('date')): ?>
                 <span class="help-block" style="color:red">
                      <strong><?php echo e($errors->first('time')); ?> </strong>
                 </span>
            <?php endif; ?>
        </div>


        

        <div class="col-lg-12 col-sm-12 <?php echo e($errors->has('color') ? ' has-error' : ''); ?>">
              <label> اللون  </label>
              <input type="color" name="color" required class="form-control m-input" value="<?php echo e($Item->color); ?>"  placeholder=" اللون ">
              <?php if($errors->has('color')): ?>
              <span class="help-block" style="color:red">
                <strong><?php echo e($errors->first('color')); ?></strong>
              </span>
              <?php endif; ?>
            </div>

        <div class="col-sm-6 <?php echo e($errors->has('file') ? ' has-error' : ''); ?>">
            <label> تصميم الدعوه <span class="text-danger">*</span> </label>
            <input class="form-control" type="file" id="formFile" name="file" />
            <img id="imgPreview" src="<?php echo e($Item->file); ?>?<?php echo e(rand()); ?>" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
            <?php if($errors->has('file')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('file')); ?></strong>
                </span>
            <?php endif; ?>
        </div>

        <div class="col-sm-6 <?php echo e($errors->has('image') ? ' has-error' : ''); ?>">
            <label> تصميم QR   </label>
            <input class="form-control" type="file" id="formFile" name="image" />
            <?php if($Item->image != null): ?>
            <img id="imgPreview" src="<?php echo e($Item->image); ?>?<?php echo e(rand()); ?>" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
            <?php endif; ?>
            <?php if($errors->has('image')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('image')); ?></strong>
                </span>
            <?php endif; ?>
        </div>

        <div class="col-sm-12 <?php echo e($errors->has('video') ? ' has-error' : ''); ?>">
            <label> video </label>
            <input class="form-control" type="file" id="formFile" name="video" accept="video/*" />
            <?php if($Item->video != null): ?>
            <video width="100%" height="240" controls>
                <source src="<?php echo e($Item->video); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <?php endif; ?>
            <?php if($errors->has('video')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('video')); ?></strong>
                </span>
            <?php endif; ?>
        </div>



    </div>


    <button type="submit" form="edit" class="btn btn-primary mr-2">
        <?php echo e(trans('home.update')); ?>

    </button>

<?php echo Form::close(); ?>

<!--end::Form-->
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/events/edit_form.blade.php ENDPATH**/ ?>