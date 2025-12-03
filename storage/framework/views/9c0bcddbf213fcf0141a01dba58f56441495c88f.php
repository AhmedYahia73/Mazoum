
    <?php
        $event_users = App\Models\CustomEventUsers::where('custom_event_id',$Item->id)->get();
    ?>



    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ImportEventUsers" style="margin-top: 30px;margin-bottom: 30px">
        تحميل ملف Excel
    </button>


    <?php if($event_users != null && $event_users->count() > 0): ?>

        



        <!--begin::Form-->
        <?php echo Form::open(['url' => "admin/update_custom_event_users",'role'=>'form','id'=>'update_event_users','method'=>'post']); ?>


            <input type="hidden" value="<?php echo e($Item->id); ?>" name="custom_event_id">

            <?php $__currentLoopData = $event_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <div class="row" style="padding-left: 5.6%;">

                    <div class="col-md-3 col-sm-3 <?php echo e($errors->has('name') ? ' has-error' : ''); ?>" style="margin-bottom: 20px">
                        <label> أسم المستخدم  </label>
                        <input type="text" name="old_event_users[<?php echo e($user->id); ?>][name]" required class="form-control m-input" value="<?php echo e($user->name); ?>"  placeholder="أسم المستخدم">
                        <?php if($errors->has('name')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('name')); ?></strong>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3 col-sm-3 <?php echo e($errors->has('mobile') ? ' has-error' : ''); ?>" style="margin-bottom: 20px">
                        <label> رقم الموبيل  </label>
                        <input type="text" name="old_event_users[<?php echo e($user->id); ?>][mobile]" class="form-control m-input" value="<?php echo e($user->mobile); ?>"  placeholder="رقم الموبيل">
                        <?php if($errors->has('mobile')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('mobile')); ?></strong>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3 col-sm-3 <?php echo e($errors->has('users_count') ? ' has-error' : ''); ?>" style="margin-bottom: 20px">
                        <label> عدد الدعوات </label>
                        <input type="number" min="1" name="old_event_users[<?php echo e($user->id); ?>][users_count]" class="form-control m-input" value="<?php echo e($user->users_count); ?>"  placeholder="عدد الدعوات">
                        <?php if($errors->has('users_count')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('users_count')); ?></strong>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3 col-sm-3 " style="margin-bottom: 20px">
                        <span class="btn btn-danger DeletingUser" name="<?php echo e($user->id); ?>" title='Delete' style="display: block;color: #FFF;cursor:pointer;margin-top: 17px;">
                            <?php echo e(trans('home.delete')); ?>

                        </span>
                    </div>

                </div>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" form="update_event_users" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                        تحديث
                    </button>
                </div>
            </div>

        <?php echo Form::close(); ?>


    <?php endif; ?>


    <h2 style="margin-bottom: 20px;font-size: 20px;"> أضافة أعضاء للحدث </h2>

    <hr style="margin-bottom: 15px;border-top: 2px dashed #CCC;">

    <?php echo Form::open(['url' => "admin/save_custom_event_users",'role'=>'form','id'=>'save_event_users','method'=>'post']); ?>


        <input type="hidden" value="<?php echo e($Item->id); ?>" name="custom_event_id">

        <div class="form repeater-default" style="">

            <div data-repeater-list="event_users" class="repeater-default_1">

                <div data-repeater-item class="align-items-center row repeater-default_2">

                    <div class="col-md-3 col-sm-3 <?php echo e($errors->has('name') ? ' has-error' : ''); ?>" style="margin-bottom: 20px">
                        <label> أسم المستخدم  </label>
                        <input type="text" name="name" required class="form-control m-input" value="<?php echo e(old('name')); ?>"  placeholder="أسم المستخدم">
                        <?php if($errors->has('name')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('name')); ?></strong>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3 col-sm-3 <?php echo e($errors->has('mobile') ? ' has-error' : ''); ?>" style="margin-bottom: 20px">
                        <label> رقم الموبيل  </label>
                        <input type="text" name="mobile" class="form-control m-input" value="<?php echo e(old('mobile')); ?>"  placeholder="رقم الموبيل">
                        <?php if($errors->has('name')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('mobile')); ?></strong>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3 col-sm-3 <?php echo e($errors->has('users_count') ? ' has-error' : ''); ?>" style="margin-bottom: 20px">
                        <label> عدد الدعوات </label>
                        <input type="number" min="1" name="users_count" required class="form-control m-input" value="<?php echo e(old('users_count')); ?>"  placeholder="عدد الدعوات">
                        <?php if($errors->has('users_count')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('users_count')); ?></strong>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3" style="margin-bottom: 20px">
                        <div data-repeater-delete="" class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill" style="display: block;height: 34px;line-height: 34px;padding: 0;">
                            <span>
                                <i class="la la-trash-o"></i>
                                <span><?php echo e(trans('home.delete')); ?></span>
                            </span>
                        </div>
                    </div>

                </div>

            </div>

            <div class="add-section">
                <div class="col-lg-12" style="padding-left: 0;margin-top: 5px;margin-bottom:20px">
                    <div data-repeater-create="" class="btn btn btn-sm btn-brand m-btn m-btn--icon m-btn--pill m-btn--wide" style="background: #82b830;color: #FFFF;padding-top: 5px;padding-left: 10px;padding-right: 10px;">
                        <span class="repeater-add">
                            <i class="la la-plus"></i>
                            <span>
                                أضافه المزيد من الأعضاء
                            </span>
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-12">
                <button type="submit" form="save_event_users" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                    حفظ
                </button>
            </div>
        </div>

    <?php echo Form::close(); ?>





    <div class="modal fade" id="ImportEventUsers" tabindex="-1" aria-labelledby="ImportEventUsersLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <?php echo Form::open(['url' => "admin/custom-event-user-import",'role'=>'form','id'=>'import_form','method'=>'post','files' => true]); ?>


                <div class="modal-header">
                    <h5 class="modal-title" id="ImportEventUsersLabel">  أضافه مستخدمين للدعوه </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">


                    <input type="hidden" name="custom_event_id" value="<?php echo e($Item->id); ?>">

                    <div class="" style="margin-top: 20px">
                        <label> الملف </label>
                        <input class="form-control" type="file" id="formFile" name="file" />
                        <?php if($errors->has('file')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('file')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                    <button type="submit" form="import_form" class="btn btn-primary"> أرسال </button>
                </div>

                <?php echo Form::close(); ?>


            </div>
        </div>
    </div>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/custom_events/add_users.blade.php ENDPATH**/ ?>