
    <?php
        $x = 1;
        $event_users = App\Models\EventUsers::where('event_id',$Item->id)->get();
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
          <button type="button" form="send_event_users" class="old_send_btn btn btn-success">
            <span> أرسال ميتا </span>
            <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
          </button>
        </b>
        <b>
            <button type="button" class="btn btn-success mr-2"  data-bs-toggle="modal" data-bs-target="#NewSendCustomMessage">
                 <span> أرسال</span>
                <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
            </button>
        </b>
        <b>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessage">
                أرسال رسالة خاصة
            </button>
        </b>
      	<b>
            <button class="btn btn-success" onclick="return SendCongratulations(<?php echo e($Item->id); ?>);">
                أرسال تهنئة
            </button>
        </b>
      	<button type="button" id="delete_selected_items_btn" class="btn btn-danger mr-2">
            حذف العناصر المختارة
        </button>

    </div>


    <?php echo Form::open(['url' => "admin/send_event_users",'role'=>'form','id'=>'send_event_users','method'=>'post','files' => true]); ?>



        <input type="hidden" name="event_id" value="<?php echo e($Item->id); ?>">


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
                            <th scope="col"  style="text-align: center;font-weight: normal;">الحالة</th>

                            <th scope="col"  style="text-align: center;font-weight: normal;">from</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">sent</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">delivered</th>
                          	<th scope="col"  style="text-align: center;font-weight: normal;">read</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">accepted</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">qr</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">refused</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">New Send</th>

                            <th scope="col"  style="text-align: center;font-weight: normal;"> # </th>

                            <th scope="col"  style="text-align: center;font-weight: normal;"> Qr Image </th>

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

                                    <td style="direction: ltr;text-align: center;" class="<?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->where('mobile',$user->mobile)->count() > 1 ? 'repeated_number' : ''); ?>">
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
                                        <?php if($user->is_new_sent == 1): ?>
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	<?php else: ?>
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span style="display:inline-block;font-weight: bold;color: #000;cursor: pointer;" data-bs-toggle="modal" data-bs-target="#editMobileModal<?php echo e($user->id); ?>">
                                            <i class="bx bx-edit-alt me-1"></i>
                                        </span>
                                    </td>

                                  	<td style="text-align: center;">
										<?php
                                      		$qr_image = App\Models\Qr_Code::where('event_user_id',$user->id)->latest()->first();
                                      	?>

                                      	<?php if($qr_image): ?>
                                      		<a href="<?php echo e(asset('qr_code/'.$qr_image->qr)); ?>" target="_blank">
                                              <img src="<?php echo e(asset('qr_code/'.$qr_image->qr)); ?>?<?php echo e(rand()); ?>" style="width:150px;height:150px">
                                      		</a>
                                      	<?php endif; ?>
                                    </td>

                                    <td>
                                      <div>
                                          <a href="<?php echo e(asset('admin/event-user-history/'.$user->id)); ?>" class="btn btn-primary" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-eye" aria-hidden="true"></i>
                                          </a>

                                          <?php if($user->scan_count < $user->users_count): ?>
                                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendLoginUser<?php echo e($user->id); ?>" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;background: green;border-color: green;">
                                              <i class="fa fa-sign-in" aria-hidden="true"></i>
                                          </button>
                                          <?php endif; ?>

                                          <button type="button" class="btn btn-info send_qr_code" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-qrcode" aria-hidden="true"></i>
                                          </button>

                                          <button type="button" class="btn btn-info send_new_qr_code" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;background-color: brown;border-color: brown;">
                                              <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                          </button>

                                          <button type="button" class="btn btn-primary is_send_event" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-bars-progress" aria-hidden="true"></i>
                                          </button>
                                          <button type="button" class="btn btn-success accept_event" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-check" aria-hidden="true"></i>
                                          </button>
                                          <button type="button" class="btn btn-danger refuse_event" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-times" aria-hidden="true"></i>
                                          </button>
                                          <button type="button" class="btn btn-info qr_is_send" name="<?php echo e($user->id); ?>" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-qrcode" aria-hidden="true"></i>
                                          </button>

                                          <button type="button" class="btn btn-primary send_location" name="<?php echo e($user->id); ?>" style="width: 80px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;background: green;border-color: green;">
                                              <i class="fa fa-location-arrow" aria-hidden="true"></i>
                                          </button>

                                          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#SendCustomCongratulationMessage<?php echo e($user->id); ?>" style="margin-bottom:5px">
                                             تهنئه
                                          </button>
                                       	  <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#SendCustomApologizeMessage<?php echo e($user->id); ?>">
                                             اعتذار
                                          </button>
                                      </div>
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
                <button type="button" form="send_event_users" class="old_send_btn btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                    <span> أرسال ميتا </span>
                    <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
                </button>
                <b>
                    <button type="button" class="btn btn-success mr-2" style="margin-left:0px;margin-bottom: 30px;"  data-bs-toggle="modal" data-bs-target="#NewSendCustomMessage">
                      <span> أرسال</span>
                      <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
                    </button>
                </b>
                <button type="button" class="delete_selected_items_btn btn btn-danger mr-2"  style="margin-left:0px;margin-bottom: 30px;">
                    حذف العناصر المختارة
                </button>
            </div>
        </div>


        <div class="modal fade" id="SendCustomMessage" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="SendCustomMessageLabel">  رسالة خاصة </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div style="margin-bottom: 25px;display: flex;justify-content: start;align-items: center;">
                        <input type="radio" id="old_send" name="sending_type" value="old_send" checked style="width: 17px;height: 17px;">
                        <label for="old_send" style="cursor: pointer;margin-right: 5px;">  أرسال ميتا   </label>

                        <input type="radio" id="new_send" name="sending_type" value="new_send" style="width: 17px;height: 17px;margin-right: 30px;">
                        <label for="new_send" style="cursor: pointer;margin-right: 5px;">أرسال </label>
                    </div>

                    <div class="">
                        <label> محتوي الرسالة </label>
                        <textarea name="message" rows="5" class="form-control" placeholder="محتوي الرسالة"><?php echo e(old('message')); ?></textarea>
                    </div>

                    <div class="" style="margin-top: 20px">
                        <label> صوره  <span class="text-danger">*</span> </label>
                        <input class="form-control" type="file" id="formFileImage" name="file" />
                        <img id="imgPreview" src="<?php echo e($Item->file); ?>?<?php echo e(rand()); ?>" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
                        <?php if($errors->has('image')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('image')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                  <button type="button" onclick="sendMessageToSelected()" class="btn btn-primary"> أرسال </button>
                </div>
              </div>
            </div>
        </div>


        <div class="modal fade" id="NewSendCustomMessage" tabindex="-1" aria-labelledby="NewSendCustomMessageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="NewSendCustomMessageLabel"> أرسال </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div style="margin-bottom: 25px;display: flex;justify-content: start;align-items: center;">
                        <input type="radio" id="image_item" name="file_type" value="image" checked style="width: 17px;height: 17px;">
                        <label for="image_item" style="cursor: pointer;margin-right: 5px;"> صوره </label>

                        <input type="radio" id="video_item" name="file_type" value="video" style="width: 17px;height: 17px;margin-right: 30px;">
                        <label for="video_item" style="cursor: pointer;margin-right: 5px;"> فيديو </label>
                    </div>


                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                  <button type="button" onclick="NewSendMessageToSelected()" class="btn btn-primary"> أرسال </button>
                </div>
              </div>
            </div>
        </div>

    <?php echo Form::close(); ?>





    <?php if($event_users != null && $event_users->count() > 0): ?>

        <?php $__currentLoopData = $event_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>


            <?php echo Form::open(['url' => "admin/send-congratulation-message",'role'=>'form','id'=>'send-congratulation-message'.$user->id,'method'=>'post','files' => true]); ?>


                <input type="hidden" name="event_id" value="<?php echo e($Item->id); ?>">

                <div class="modal fade" id="SendCustomCongratulationMessage<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="SendCongratulationMessageLabel<?php echo e($user->id); ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendCongratulationMessageLabel<?php echo e($user->id); ?>">
                            ارسال تهنئه للعضو
                            (  <?php echo e($user->name); ?> )
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">

                        <div class="modal-body">
                            <div class="">
                                <label> محتوي الرسالة </label>
                                <textarea name="msg1" id="msg_v1_<?php echo e($user->id); ?>" rows="5" class="form-control msg1" placeholder="محتوي الرسالة"><?php echo e(old('msg1')); ?></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="submit" form="send-congratulation-message<?php echo e($user->id); ?>" class="btn btn-primary" data-id="<?php echo e($user->id); ?>"> أرسال </button>
                        </div>
                        </div>
                    </div>
                </div>

            <?php echo Form::close(); ?>



            <?php echo Form::open(['url' => "admin/send-apologize-message",'role'=>'form','id'=>'send-apologize-message'.$user->id,'method'=>'post','files' => true]); ?>


                <input type="hidden" name="event_id" value="<?php echo e($Item->id); ?>">

                <div class="modal fade" id="SendCustomApologizeMessage<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="SendApologizeMessageLabel<?php echo e($user->id); ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendApologizeMessageLabel<?php echo e($user->id); ?>">
                            ارسال اعتذار للعضو
                            (  <?php echo e($user->name); ?> )
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">

                        <div class="modal-body">
                            <div class="">
                                <label> محتوي الرسالة </label>
                                <textarea name="msg2" id="msg_v2_<?php echo e($user->id); ?>" rows="5" class="form-control msg2" placeholder="محتوي الرسالة"><?php echo e(old('msg2')); ?></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="submit" form="send-apologize-message<?php echo e($user->id); ?>"  class="btn btn-primary" data-id="<?php echo e($user->id); ?>"> أرسال </button>
                        </div>
                        </div>
                    </div>
                </div>

            <?php echo Form::close(); ?>



            <?php echo Form::open(['url' => "admin/login-user/".$user->id,'role'=>'form','id'=>'send-login-user'.$user->id,'method'=>'get','files' => true]); ?>


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



            <div class="modal fade" id="editMobileModal<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendCustomMessageLabel"> تعديل رقم الموبيل </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <?php echo Form::open(['url' => "admin/update-user-mobile", 'role'=>'form','id'=>'editFormMobileModal'.$user->id,'method'=>'post']); ?>


                            <input type="hidden" name="event_user_id" value="<?php echo e($user->id); ?>">

                            <div class="" style="margin-bottom: 20px">
                                <label> عدد الدعوات	 </label>
                                <input type="text" class="form-control" name="users_count" value="<?php echo e($user->users_count); ?>" placeholder="عدد الدعوات  " required>
                            </div>

                            <div class="" style="margin-bottom: 20px">
                                <label>   أسم المستخدم	 </label>
                                <input type="text" class="form-control" name="name" value="<?php echo e($user->name); ?>" placeholder="أسم المستخدم	  " required>
                            </div>

                            <div class="">
                                <label> رقم الموبيل </label>
                                <input type="text" class="form-control" name="mobile" value="<?php echo e($user->mobile); ?>" placeholder="رقم الموبيل" required>
                            </div>

                            <?php echo Form::close(); ?>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="submit" form="editFormMobileModal<?php echo e($user->id); ?>" class="btn btn-primary"> تحديث </button>
                        </div>
                    </div>
                </div>
            </div>


        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php endif; ?>

<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/events/send_events.blade.php ENDPATH**/ ?>