
    <?php
        $x = 1;
        $event_users = App\Models\EventUsers::where('event_id',$Item->id)->get();
    ?>


    <div style="margin-bottom: 20px">
        <b>
            <label class="btn btn-warning">
                <input type="checkbox" for="checkbox_select" id="select_all" name="select_all">
                <span style="display: inline-block;padding-right:10px">
                    أختر الكل
                </span>
            </label>
        </b>
        <b>
            <button class="btn btn-success" onclick="return SendCongratulations(<?php echo e($Item->id); ?>);">
                أرسال تهنئة
            </button>
        </b>
        <b>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessage">
                أرسال رسالة خاصة
            </button>
        </b>
    </div>


    <?php echo Form::open(['url' => "assistant_panel/send_event_users",'role'=>'form','id'=>'send_event_users','method'=>'post']); ?>



        <input type="hidden" name="event_id" value="<?php echo e($Item->id); ?>">


        <div class="row">
            <div class="col-lg-12">

              <div class="table-responsive">

                <table class="table zero-configuration">
                    <thead>
                        <tr>
                            <th scope="col"  style="text-align: center;font-weight: normal;">#</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">عدد الدعوات</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;"> أسم المستخدم </th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">رقم الهاتف </th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">الحالة</th>

                            <th scope="col"  style="text-align: center;font-weight: normal;">from</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">sent</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">delivered</th>
                          	<th scope="col"  style="text-align: center;font-weight: normal;">read</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">accepted</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">qr</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">refused</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;"> error </th>

                            <th scope="col">#</th>
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
                                    <td style="text-align: center;">
                                        <?php echo e($user->status); ?>

                                    </td>

                                  	<td style="text-align: center;">
                                        <?php echo e($user->sent_from); ?>

                                    </td>
                                  	<td style="text-align: center;">
                                        <?php if($user->is_sent == 'yes'): ?>
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	<?php else: ?>
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        <?php endif; ?>
                                    </td>

                                  	<td style="text-align: center;">
                                        <?php if($user->is_delivered == 'yes'): ?>
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	<?php else: ?>
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        <?php endif; ?>
                                    </td>

                                  	<td style="text-align: center;">
                                        <?php if($user->is_read == 'yes'): ?>
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	<?php else: ?>
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        <?php endif; ?>
                                    </td>

                                  	<td style="text-align: center;">
                                        <?php if($user->is_accepted == 'yes'): ?>
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	<?php else: ?>
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        <?php endif; ?>
                                    </td>

                                  	<td style="text-align: center;">
                                        <?php if($user->qr_sent == 'yes'): ?>
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	<?php else: ?>
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        <?php endif; ?>
                                    </td>

                                  	<td style="text-align: center;">
                                        <?php if($user->is_refused == 'yes'): ?>
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	<?php else: ?>
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        <?php endif; ?>
                                    </td>
                                  	<td style="text-align: center;">

                                    </td>

                                    <td>
                                      	<a href="<?php echo e(asset('assistant_panel/event-user-history/'.$user->id)); ?>" class="btn btn-primary" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>

                                        <?php if($user->scan_count < $user->users_count): ?>
                                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendLoginUser<?php echo e($user->id); ?>" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;background: green;border-color: green;">
                                              <i class="fa fa-sign-in" aria-hidden="true"></i>
                                          </button>
                                        <?php endif; ?>

                                      	<button type="button" class="btn btn-primary send_qr_code" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                            <i class="fa fa-qrcode" aria-hidden="true"></i>
                                        </button>
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


        <?php if($event_users != null && $event_users->count() > 0): ?>
        <div class="row"  style="margin-top: 20px">
            <div class="col-lg-12">
                <button type="submit" form="send_event_users" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                    أرسال
                </button>
            </div>
        </div>
        <?php endif; ?>


        <div class="modal fade" id="SendCustomMessage" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="SendCustomMessageLabel">  رسالة خاصة </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="">
                      <label> محتوي الرسالة </label>
                      <textarea name="message" rows="5" class="form-control" placeholder="محتوي الرسالة"><?php echo e(old('message')); ?></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                  <button type="button" onclick="sendMessageToSelected()" class="btn btn-primary"> أرسال </button>
                </div>
              </div>
            </div>
        </div>

    <?php echo Form::close(); ?>




    <?php if($event_users != null && $event_users->count() > 0): ?>

        <?php $__currentLoopData = $event_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>


            <?php echo Form::open(['url' => "assistant_panel/login-user/".$user->id,'role'=>'form','id'=>'send-login-user'.$user->id,'method'=>'get','files' => true]); ?>


                <input type="hidden" name="event_id" value="<?php echo e($Item->id); ?>">

                <div class="modal fade" id="SendLoginUser<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="SendLoginUserLabel<?php echo e($user->id); ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendLoginUserLabel<?php echo e($user->id); ?>">
                            هل تريد حقا تاكيد دخول العضو
                            (  <?php echo e($user->name); ?> )
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">

                        <div class="modal-body">
                            <div class="">
                                <label> عدد الدعوات	  </label>
                                <select name="users_count" class="form-control m-bootstrap-select m_selectpicker" required>
                                    <option value="" disabled selected> اختر عدد الدعوات </option>
                                    <?php for($count=1;$count <= ($user->users_count - $user->scan_count); $count++): ?>
                                        <option value="<?php echo e($count); ?>"> <?php echo e($count); ?> </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="submit" form="send-login-user<?php echo e($user->id); ?>" class="btn btn-primary" data-id="<?php echo e($user->id); ?>"> دخول </button>
                        </div>
                        </div>
                    </div>
                </div>

            <?php echo Form::close(); ?>


        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php endif; ?>


<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/assistant/events/event-user-invitations.blade.php ENDPATH**/ ?>