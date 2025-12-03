

    <div class="row">

        <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('title') ? ' has-error' : ''); ?>">
            <label> عنوان الحدث </label>
            <input type="text" name="title" required class="form-control m-input" value="<?php echo e($Item->title); ?>"  placeholder="عنوان الحدث ">
            <?php if($errors->has('title')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('title')); ?></strong>
                </span>
            <?php endif; ?>
        </div>

        <div class="col-md-6 col-sm-6 <?php echo e($errors->has('user_id') ? ' has-error' : ''); ?>">
            <label> المستخدمين   </label>
            <select name="user_id" id="user_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
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
      
      	<div class="col-sm-12 <?php echo e($errors->has('have_reminder') ? ' has-error' : ''); ?>">
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

        <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('lat') ? ' has-error' : ''); ?>">
            <label> دوائر العرض </label>
            <input type="text" name="lat" class="form-control m-input" value="<?php echo e($Item->lat); ?>"  placeholder="دوائر العرض ">
            <?php if($errors->has('lat')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('lat')); ?></strong>
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


        <div class="col-sm-6 <?php echo e($errors->has('file') ? ' has-error' : ''); ?>">
            <label> صوره  <span class="text-danger">*</span> </label>
            <input class="form-control" type="file" id="formFile" name="file" />
            <img id="imgPreview" src="<?php echo e($Item->file); ?>?<?php echo e(rand()); ?>" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
            <?php if($errors->has('file')): ?>
                <span class="help-block" style="color:red">
                    <strong><?php echo e($errors->first('file')); ?></strong>
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


    </div>

<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/assistant/events/edit_form.blade.php ENDPATH**/ ?>