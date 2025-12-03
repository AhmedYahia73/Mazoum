
    <?php
        $x = 1;
        $event_users = App\Models\CustomEventUsers::where('custom_event_id',$Item->id)->get();
    ?>


    <div style="margin-bottom: 20px">
        <b>
            <label class="btn btn-warning">
                <input type="checkbox" for="checkbox_select" id="select_all" name="select_all">
                <span style="display: inline-block;padding-right:10px">
                  <span> أختر الكل </span>
                </span>
            </label>
        </b>

        <b>
            <button type="button" class="new_send_btn btn btn-success mr-2">
                أرسال حديث
            </button>
        </b>

      	<button type="button" id="delete_selected_items_btn" class="btn btn-danger mr-2">
            حذف العناصر المختارة
        </button>

    </div>


    <?php echo Form::open(['url' => "admin/send_event_users",'role'=>'form','id'=>'send_event_users','method'=>'post','files' => true]); ?>



        <input type="hidden" name="custom_event_id" value="<?php echo e($Item->id); ?>">


        <div class="row">
            <div class="col-lg-12">

              <div class="">

                <table class="table" id="send_events_table">
                    <thead>
                        <tr>
                            <th scope="col"  style="text-align: center;font-weight: normal;">#</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">عدد الدعوات</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;"> أسم المستخدم </th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">رقم الهاتف </th>

                            <th scope="col"  style="text-align: center;font-weight: normal;"> Qr Image </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if($event_users != null && $event_users->count() > 0): ?>

                            <?php $__currentLoopData = $event_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <tr>
                                    <th scope="row"  style="text-align: center;">
                                      <div style="display: flex;align-items: center;justify-content: center;">
                                         <span style="font-size: 20px;"> <?php echo e($x); ?> </span>
                                         <input type="checkbox" class="check_items" name="users[<?php echo e($x); ?>][id]" value="<?php echo e($user->id); ?>" id="user<?php echo e($user->id); ?>" style="cursor: pointer;margin-right: 5px;width: 20px;height: 20px;">
                                      </div>
                                    </th>
                                    <td style="text-align: center;">
                                        <input type="text" name="users[<?php echo e($x); ?>][users_count]" value="<?php echo e($user->users_count ? $user->users_count : 1); ?>"  onkeypress="return isNumberKey(event)" class="form-control" style="max-width:70px;text-align:center;cursor: pointer">
                                    </td>
                                    <td style="text-align: center;">
                                        <label for="user<?php echo e($user->id); ?>" style="cursor: pointer">
                                            <?php echo e($user->name); ?>

                                        </label>
                                    </td>
                                    <td style="direction: ltr;text-align: center;">
                                        <?php echo e($user->mobile); ?>

                                    </td>


                                  	<td class="qr-cell">
                                        <a href="<?php echo e($user->qr); ?>" download="<?php echo e($user->name); ?>">
                                            <img src="<?php echo e($user->qr); ?>" style="max-width:150px">
                                        </a>
                                    </td>


                                </tr>





                                <?php $x = $x + 1; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php else: ?>
                        <tr>
                            <td colspan="6">
                                لا يوجد مستخدمين
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

              </div>

            </div>
        </div>


        <div class="row"  style="margin-top: 20px">
            <div class="col-lg-12">

                <b>
                    <button type="button" class="new_send_btn btn btn-success mr-2" style="margin-left:0px;margin-bottom: 30px;">
                        أرسال حديث
                    </button>
                </b>

                <button type="button" class="delete_selected_items_btn btn btn-danger mr-2"  style="margin-left:0px;margin-bottom: 30px;">
                    حذف العناصر المختارة
                </button>
            </div>
        </div>


    <?php echo Form::close(); ?>




<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/custom_events/send_event_users.blade.php ENDPATH**/ ?>