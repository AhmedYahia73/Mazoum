
<?php echo Form::model($Item, [ 'route' => ['admin.custom_events.update' , $Item->id ] , 'method' => 'patch', 'files'=>true, 'role'=>'form','id'=>'edit', 'class'=> 'm-form m-form--fit m-form--label-align-left' ]); ?>


    <?php echo e(csrf_field()); ?>


    <input type="hidden" name="id" value="<?php echo e($Item->id); ?>">

    <div class="card-body">
        <div class="row" style="margin-top: 30px">


            <div class="col-md-6 col-sm-6 <?php echo e($errors->has('assistant_id') ? ' has-error' : ''); ?>">
                <label> الموظفين  <span class="text-danger">*</span> </label>
                <select name="assistant_id" id="assistant_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
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


            <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('title') ? ' has-error' : ''); ?>">
                <label> عنوان الدعوه </label>
                <input type="text" name="title" required class="form-control m-input" value="<?php echo e($Item->title); ?>"  placeholder="عنوان الدعوه ">
                <?php if($errors->has('title')): ?>
                    <span class="help-block" style="color:red">
                        <strong><?php echo e($errors->first('title')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>




          	<div class="col-lg-6 col-sm-6 <?php echo e($errors->has('color') ? ' has-error' : ''); ?>">
              <label> اللون  </label>
              <input type="color" name="color" required class="form-control m-input" value="<?php echo e($Item->color); ?>"  placeholder=" اللون ">
              <?php if($errors->has('color')): ?>
              <span class="help-block" style="color:red">
                <strong><?php echo e($errors->first('color')); ?></strong>
              </span>
              <?php endif; ?>
            </div>

            <div class="col-lg-12 col-sm-12 <?php echo e($errors->has('address') ? ' has-error' : ''); ?>">
                <label> موقع الحدث </label>
                <input type="text" name="address" required class="form-control m-input" value="<?php echo e($Item->address); ?>"  placeholder="موقع الحدث ">
                <?php if($errors->has('address')): ?>
                    <span class="help-block" style="color:red">
                        <strong><?php echo e($errors->first('address')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="col-sm-6 <?php echo e($errors->has('date') ? ' has-error' : ''); ?>">
                <label> تاريخ الحدث <span class="text-danger">*</span> </label>
                <input type="text" name="date" required class="form-control m-input" value="<?php echo e($Item->date); ?>"  placeholder="YYYY-MM-DD" id="flatpickr-date1">
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

            <div class="col-md-6 col-sm-6 <?php echo e($errors->has('language') ? ' has-error' : ''); ?>">
                <label> اللغه  <span class="text-danger">*</span> </label>
                <select name="language" id="language" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                    <option value="" selected="true"> اللغه </option>
                    <option value="ar" <?php if($Item->language == 'ar'): ?> <?php echo e('selected'); ?> <?php endif; ?>> عربي </option>
                    <option value="en" <?php if($Item->language == 'en'): ?> <?php echo e('selected'); ?> <?php endif; ?>> انجليزي </option>
                </select>
                <?php if($errors->has('language')): ?>
                    <span class="help-block" style="color:red">
                        <strong><?php echo e($errors->first('language')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="col-sm-6 <?php echo e($errors->has('image') ? ' has-error' : ''); ?>">
                <label> صوره    </label>
                <input class="form-control" type="file" id="formFile"  name="image" />
                <?php if($Item->image != null): ?>
                <br>
                <img src="<?php echo e($Item->image); ?>?<?php echo e(rand()); ?>" style="width:200px">
                <?php endif; ?>
                <?php if($errors->has('image')): ?>
                    <span class="help-block" style="color:red">
                        <strong><?php echo e($errors->first('image')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="col-lg-12" style="margin-bottom:20px">
                <button type="submit" form="edit" class="btn btn-success" style="margin-top:20px;margin-bottom: 0px;"><?php echo e(trans('home.update')); ?></button>
            </div>

        </div>
    </div>


<?php echo Form::close(); ?>

<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/custom_events/edit_form.blade.php ENDPATH**/ ?>