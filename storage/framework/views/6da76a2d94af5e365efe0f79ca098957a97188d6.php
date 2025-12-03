<?php $__env->startSection('title',$title); ?>

<?php $__env->startSection('header'); ?>

    <style>
        td , th {
            text-align:center !important
        }

        td label {
            display: block;
            text-align: right;
        }

        .new_send_user_row {
            background-color: #fdac41 !important;
        }

        .is_send_congratulation {
            background-color: green !important;
        }

        .new_send_user_row  td , .is_send_congratulation  td {
            color: #FFF !important
        }
    </style>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>

    <!-- Cards with badge -->
    <div class="row justify-content-center" style="margin-top: 20px">

        <div class="col-12">
            <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>


      	<div class="col-12" style="margin-bottom: 20px">
          <b>
              <label class="btn btn-warning">
                  <input type="checkbox" for="checkbox_select" id="select_all" name="select_all">
                  <span style="display: inline-block;padding-right:10px">
                      أختر الكل
                  </span>
              </label>
          </b>

          <?php if(!isset($is_qr_page) && isset($type) && $type != 'confirmed_event_details'): ?>
          <b>
            <button type="button" onclick="return sendToSelected();" class="btn btn-success">
              <span> أرسال ميتا </span>
              <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
            </button>
          </b>

          <b>
            <button type="button" class="btn btn-success mr-2" style="margin-left:0px;"  data-bs-toggle="modal" data-bs-target="#NewSendCustomMessage">
                أرسال
            </button>
          </b>
          <?php endif; ?>

          <b>
            <button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 0px;">
              حذف المستخدمين
            </button>
          </b>



          <?php if(isset($type) && ($type == 'confirmed_event_details')): ?>
          <b>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendRememberMessage">
                تذكير
            </button>
          </b>
          <?php endif; ?>


          <b>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessage">
                أرسال رسالة خاصة
            </button>
          </b>

          <?php if(isset($type) && $type != 'failed'): ?>
          <b>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomCongratulationsMessage">
                أرسال تهنئه
            </button>
          </b>
          <?php endif; ?>


        </div>


      	<?php echo Form::open(['role'=>'form','id'=>'all_users','method'=>'post','files' => true]); ?>



            <input type="hidden" name="event_id" value="<?php echo e($Item->id); ?>">


            <div class="col-12">
                <div class="card"  style="padding: 20px">

                    <h3>
                        <?php echo e($title); ?>

                    </h3>

                    <div>

                        <div class="">

                            <table class="table" id="all_users_table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">عدد الدعوات</th>
                                        <th scope="col">  تم التاكيد  </th>
                                        <th scope="col"> أسم المستخدم </th>
                                        <th scope="col">رقم الهاتف </th>
                                        <th scope="col" style="text-align: center;">الحالة</th>
                                        <th scope="col" style="text-align: center;">QR Send</th>
                                      	<th scope="col" style="text-align: center;">QR Scan</th>
                                      	<th scope="col"> تاريخ ووقت التاكيد </th>
                                      	<th scope="col"> تعديل رقم الموبيل </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php $x = 1; ?>

                                    <?php if($data != null && $data->count() > 0): ?>

                                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <?php
                                                $row_class = '';

                                                if(isset($is_qr_page) && $is_qr_page == 'yes' && $user->is_send_congratulation == 1) {
                                                    $row_class = 'is_send_congratulation';
                                                } elseif($user->is_new_sent == 1) {
                                                    $row_class = 'new_send_user_row';
                                                }
                                            ?>

                                            <tr  class="<?php echo e($row_class); ?>">
                                                <th scope="row">
                                                    <div style="display: flex;align-items: center;justify-content: center;">
                                                        <span style="font-size: 20px;"> <?php echo e($x); ?> </span>
                                                        <input type="checkbox" class="check_items" name="users[<?php echo e($x); ?>][id]" value="<?php echo e($user->id); ?>" id="user<?php echo e($user->id); ?>" style="cursor: pointer;margin-right: 5px;width: 20px;height: 20px;">
                                                    </div>
                                                </th>

                                                <td>
                                                    <?php echo e($user->users_count); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($user->scan_count); ?>

                                                </td>

                                                <td>
                                                    <label for="user<?php echo e($user->id); ?>" style="cursor: pointer">
                                                        <?php echo e($user->name); ?>

                                                    </label>
                                                </td>

                                                <td style="direction: ltr;">
                                                   <span> <?php echo e($user->mobile); ?> </span>
                                                </td>

                                                <td style="text-align: center;">
                                                    <?php echo e($user->status); ?>

                                                </td>

                                              	<td style="text-align: center;">
                                                    <?php echo e($user->qr_sent == 'yes' ? 'نعم': 'لا'); ?>

                                                </td>

                                              	<td style="text-align: center;">
                                                    <?php echo e($user->scan == 'yes' ? 'نعم': 'لا'); ?>

                                                </td>

                                                <td>
												   <?php echo e($user->confirmed_at != null ? Carbon\Carbon::parse($user->confirmed_at)->format('Y-m-d h:i A') : null); ?>

                                                </td>

                                              	<td>
                                                	<span style="display:inline-block;font-weight: bold;color: #000;cursor: pointer;" data-bs-toggle="modal" data-bs-target="#editMobileModal<?php echo e($user->id); ?>">
                                                  		<i class="bx bx-edit-alt me-1"></i>
                                                   </span>
                                                </td>

                                            </tr>

                                            <?php $x = $x + 1; ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php else: ?>
                                    <tr>
                                        <td colspan="9">
                                            لا يوجد مستخدمين
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>


            <div class="row"  style="margin-top: 20px">
                <div class="col-lg-12">

                    <?php if(!isset($is_qr_page) && isset($type) && $type != 'confirmed_event_details'): ?>
                    <button type="button" onclick="return sendToSelected();" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                        <span> أرسال ميتا </span>
                    	<i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
                    </button>

                    <b>
                      <button type="button" class="btn btn-success mr-2" style="margin-left:0px;margin-bottom: 30px;"  data-bs-toggle="modal" data-bs-target="#NewSendCustomMessage">
                          أرسال
                      </button>
                    </b>
                    <?php endif; ?>

                    <button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 30px;">
                        حذف المستخدمين
                    </button>

                    <?php if(isset($type) && ($type == 'confirmed_event_details')): ?>
                    <b>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendRememberMessage" style="margin-left:0px;margin-bottom: 30px;">
                            تذكير
                        </button>
                    </b>
                    <?php endif; ?>

                    <b>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessage" style="margin-left:0px;margin-bottom: 30px;">
                            أرسال رسالة خاصة
                        </button>
                    </b>

                    <?php if(isset($type) && $type != 'failed'): ?>
                    <b>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomCongratulationsMessage" style="margin-left:0px;margin-bottom: 30px;">
                            أرسال تهنئه
                        </button>
                    </b>
                    <?php endif; ?>

                </div>
            </div>




            <div class="modal fade" id="SendRememberMessage" tabindex="-1" aria-labelledby="SendRememberMessageLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendRememberMessageLabel">  تذكير   </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div style="margin-bottom: 25px;display: flex;justify-content: start;align-items: center;">
                                <input type="radio" id="old_send2" name="sending_type2" value="old_send" checked style="width: 17px;height: 17px;">
                                <label for="old_send2" style="cursor: pointer;margin-right: 5px;">  أرسال ميتا   </label>

                                <input type="radio" id="new_send2" name="sending_type2" value="new_send" style="width: 17px;height: 17px;margin-right: 30px;">
                                <label for="new_send2" style="cursor: pointer;margin-right: 5px;">أرسال </label>
                            </div>

                          	<div style="margin-bottom: 25px">
                                <label> التاريخ </label>
                                <input type="text" name="date" class="form-control" value="<?php echo e(old('date')); ?>" placeholder="التاريخ">
                            </div>

                          	<div style="margin-bottom: 25px">
                                <label> الوقت </label>
                                <input type="text" name="time" class="form-control" value="<?php echo e(old('time')); ?>" placeholder="الوقت">
                            </div>

                            <div style="margin-bottom: 25px">
                                <label> محتوي الرسالة </label>
                                <textarea name="message2" rows="5" class="form-control" placeholder="محتوي الرسالة"><?php echo e(old('message2')); ?></textarea>
                            </div>

                            <div class="" style="margin-top: 20px">
                                <label> صوره </label>
                                <input class="form-control" type="file" id="formFile" name="file2" />
                                <img id="imgPreview" src="<?php echo e($Item->file); ?>?<?php echo e(rand()); ?>" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
                                <?php if($errors->has('image2')): ?>
                                    <span class="help-block" style="color:red">
                                        <strong><?php echo e($errors->first('image2')); ?></strong>
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
                            <button type="button" onclick="sendMessageToSelected2()" class="btn btn-primary"> أرسال </button>
                        </div>
                    </div>
                </div>
            </div>



            <div class="modal fade" id="SendCustomCongratulationsMessage" tabindex="-1" aria-labelledby="SendCustomCongratulationsMessageLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendCustomCongratulationsMessageLabel"> تهنئه   </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div style="margin-bottom: 25px;display: flex;justify-content: start;align-items: center;">
                                <input type="radio" id="old_send3" name="sending_type" value="old_send" checked style="width: 17px;height: 17px;">
                                <label for="old_send3" style="cursor: pointer;margin-right: 5px;">  أرسال ميتا   </label>

                                <input type="radio" id="new_send3" name="sending_type" value="new_send" style="width: 17px;height: 17px;margin-right: 30px;">
                                <label for="new_send3" style="cursor: pointer;margin-right: 5px;">أرسال </label>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="button" onclick="sendCongratulationMessageToSelected2()" class="btn btn-primary"> أرسال </button>
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





        <?php if($data != null && $data->count() > 0): ?>

            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

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

    </div>

<?php $__env->stopSection(); ?>





<?php $__env->startSection('footer'); ?>

    <script type="text/javascript">

        $(document).ready(function () {

            $('#select_all').click(function () {
               $('.check_items').prop('checked', this.checked);
            });


            $('.new_send_btn').click(function() {

                swal({
                        title: "هل تريد تاكيد أرسال الدعوات لهولاء المستخدمين ",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "نعم",
                        cancelButtonText: "لا",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            $('#all_users').attr('action','<?php echo e(asset('admin/new-send-event-invitation')); ?>').attr('method','post').submit();
                        }
                    }
                );
            });




        });

    </script>

	<script>

        function sendToSelected() {
            swal({
                title: "هل تريد حقًا ارسال الدعوه",
                text: "بعد الأرسال ، لا يمكنك العودة.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                closeOnConfirm: true,
                closeOnCancel: true
              },
              function (isConfirm) {
                if (isConfirm) {
                  $('#all_users').attr('action', '<?php echo e(url("admin/send_event_users")); ?>').submit();
                }
              });
        }



      	function deleteToSelected() {
            swal({
                title: "هل تريد حقًا حذف المستخدمين المختارين",
                text: "بعد الحذف ، لا يمكنك العودة.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                closeOnConfirm: true,
                closeOnCancel: true
              },
              function (isConfirm) {
                if (isConfirm) {
                  $('#all_users').attr('action', '<?php echo e(url("admin/delete_event_users")); ?>').submit();
                }
              });
        }



        function sendMessageToSelected() {
            swal({
                title: "هل تريد حقًا تاكيد أرسال تذكير",
                text: "بعد الأرسال ، لا يمكنك العودة.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                closeOnConfirm: true,
                closeOnCancel: true
              },
              function (isConfirm) {
                if (isConfirm) {
                  $('#all_users').attr('action', '<?php echo e(url("admin/remember-users-to-event")); ?>').submit();
                }
              });
        }



        function sendMessageToSelected2() {
            swal({
                title: "هل تريد حقًا ارسال رسالة",
                text: "بعد الأرسال ، لا يمكنك العودة.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                closeOnConfirm: true,
                closeOnCancel: true
              },
              function (isConfirm) {
                if (isConfirm) {
                  $('#all_users').attr('action', '<?php echo e(url("admin/send-custom-message")); ?>').submit();
                }
              });
        }



        function sendCongratulationMessageToSelected2() {
            swal({
                title: "هل تريد حقًا ارسال تهنئة",
                text: "بعد الأرسال ، لا يمكنك العودة.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                closeOnConfirm: true,
                closeOnCancel: true
              },
              function (isConfirm) {
                if (isConfirm) {
                  $('#all_users').attr('action', '<?php echo e(url("admin/send-congratulation-messages")); ?>').submit();
                }
              });
        }



        function NewSendMessageToSelected() {
            swal({
                title: "هل تريد حقًا ارسال ",
                text: "بعد الأرسال ، لا يمكنك العودة.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                closeOnConfirm: true,
                closeOnCancel: true
              },
              function (isConfirm) {
                if (isConfirm) {
                    $('#all_users').attr('action','<?php echo e(asset('admin/new-send-event-invitation')); ?>').attr('method','post').submit();
                    
                }
              });
        }


    </script>

	 <script>
        'use strict';
        $(function () {

          	var all_users_table = $('#all_users_table');

            if (all_users_table.length) {
                var dt_responsive4 = all_users_table.DataTable({
                    responsive: 0,
                    paging: 0,
                    iDisplayLength: 1000,
                    dom: 'Bfrtip',
                    buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    }
                ],
                    "bSort": false
                });
            }



        });
    </script>



<?php $__env->stopSection(); ?>




<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/event_details/users.blade.php ENDPATH**/ ?>