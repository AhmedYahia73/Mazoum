<?php $__env->startSection('title',$title); ?>

<?php $__env->startSection('header'); ?>


<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>

    <!-- Cards with badge -->
    <div class="row justify-content-center" style="margin-top: 20px">

        <div class="col-12">
            <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <div class="col-12">
            <div class="card"  style="padding: 20px">

                <h3>
                    <?php echo e($title); ?>

                </h3>

              	<!--begin::Form-->
              <?php echo Form::open(['url' => "admin/event_messages_search",'role'=>'form','id'=>'event_users_search','method'=>'get']); ?>



                <div class="row" style="border: 2px solid #777;padding: 40px 20px 20px;margin-top: 25px;margin-bottom: 35px;">


                   <input type="hidden" name="type" value="<?php echo e($type); ?>">
                   <input type="hidden" name="event_id" value="<?php echo e($Item->id); ?>">

                   <div class="col-md-4 col-sm-4 <?php echo e($errors->has('name') ? ' has-error' : ''); ?>" style="margin-bottom: 0px">
                    <label> أسم المستخدم  </label>
                    <input type="text" name="name" class="form-control m-input" value="<?php echo e(isset(request()->name) ? request()->name : ''); ?>"  placeholder="أسم المستخدم">
                    <?php if($errors->has('name')): ?>
                    <span class="help-block" style="color:red">
                      <strong><?php echo e($errors->first('name')); ?></strong>
                    </span>
                    <?php endif; ?>
                  </div>

                  <div class="col-md-4 col-sm-4 <?php echo e($errors->has('mobile') ? ' has-error' : ''); ?>" style="margin-bottom: 0px">
                    <label> رقم الموبيل </label>
                    <input type="text" name="mobile" class="form-control m-input" value="<?php echo e(isset(request()->mobile) ? request()->mobile : ''); ?>"  placeholder="رقم الموبيل">
                    <?php if($errors->has('mobile')): ?>
                    <span class="help-block" style="color:red">
                      <strong><?php echo e($errors->first('mobile')); ?></strong>
                    </span>
                    <?php endif; ?>
                  </div>

                  <div class="col-md-4">
                    <label style="visibility: hidden;"> بحث  </label>
                    <button type="submit" form="event_users_search" class="btn btn-primary" style="margin-left:0px;margin-bottom: 30px;width:100%">
                      بحث
                    </button>
                  </div>

                </div>

              <?php echo Form::close(); ?>



              <?php echo Form::open(['url' => "admin/delete-messages",'role'=>'form','id'=>'delete-messages','method'=>'post']); ?>



                <div>

                  	<b>
                      <button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 20px;">
                        حذف الرسائل
                      </button>
                    </b>

                    <div class="row justify-content-center">

                        <?php if($messages != null && $messages->count() > 0): ?>

                          <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                      		<?php if($type == 'congrate_message'): ?>
                      			<?php $replay = App\Models\CongratulationMessages::where('message_id',$message->id)->first(); ?>
                      		<?php else: ?>
                      			<?php $replay = App\Models\EventMessages::where('message_id',$message->id)->first(); ?>
                      		<?php endif; ?>

                            <div class="col-md-12" style="margin-bottom:20px;background: #fbf8f8;padding-left: 0;padding-right: 0;position:relative">
                                <div class="card-header" style="color:#000;margin-bottom: 10px;display: flex;justify-content: space-between;padding-left: 50px;">
                                    <span>
                                        <?php echo e($message->name); ?>

                                    </span>
                                    <span>
                                        <?php echo e($message->mobile); ?>

                                    </span>
                                </div>
                                <div class="card-body">
                                  <p class="card-text" style="color:#000">
                                      <?php echo e($message->message); ?>

                                  </p>
                                </div>

                              	<!--
                                <a onclick="return DeletingModal(<?php echo e($message->id); ?>);" class="DeletingModal" name="<?php echo e($message->id); ?>" href="javascript:void(0);" style="position:absolute;left: 20px;top: 20px;">
                                    <i class="bx bx-trash me-1"></i> <?php echo e(trans('home.Delete')); ?>

                                </a>
								-->

                                <a class="MultipleDeletingModal" name="<?php echo e($message->id); ?>" href="javascript:void(0);" style="position:absolute;left: 10px;top: 20px;">
                                  <input type="hidden"   name="messags_ids[<?php echo e($message->id); ?>][type]" value="<?php echo e($type == 'congrate_message' ? 'congrate' : 'appolize'); ?>" style="width: 20px;height: 20px;">
                                  <input type="checkbox" name="messags_ids[<?php echo e($message->id); ?>][id]" value="<?php echo e($message->id); ?>" style="width: 20px;height: 20px;">
                                </a>



                              	<?php if($replay != null): ?>
                                <div class="" style="margin-bottom:20px;background: #bf9494;padding-left: 0;padding-right: 0;position:relative;padding-top: 10px;">
                                    <h3 style="font-size: 20px;font-weight: bold;color: #000;margin-bottom: 0;margin-right: 20px;border-bottom: 1px solid #777;padding-bottom: 10px;"> رد الرساله </h3>
                                    <div class="card-header" style="color:#000;margin-bottom: 0;padding-top: 10px;padding-bottom: 10px;">
                                        <?php echo e($replay->name); ?>

                                    </div>
                                    <div class="card-body">
                                      <h5 class="card-title" style="color:#000;margin-bottom: 5px !important;">
                                          <?php echo e($replay->mobile); ?>

                                      </h5>
                                      <p class="card-text" style="color:#000">
                                          <?php echo e($replay->message); ?>

                                      </p>
                                    </div>
                                    <a onclick="return DeletingModal(<?php echo e($replay->id); ?>);" class="DeletingModal" name="<?php echo e($replay->id); ?>" href="javascript:void(0);" style="position: absolute;left: 20px;bottom: 10px;color: #000;">
                                        <i class="bx bx-trash me-1"></i> <?php echo e(trans('home.Delete')); ?>

                                    </a>
                                </div>
                              <?php endif; ?>

                            </div>



                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="col-md-12" style="margin-bottom:20px;">
                            <div class="card-body">
                              <p class="card-text" style="text-align: center;color: #FFF;font-size: 55px;margin-top: 150px;">
                                  عفوا لا يوجد اي رسائل تهنئه
                              </p>
                            </div>
                          </div>
                        <?php endif; ?>

                      </div>

                </div>

              <?php echo Form::close(); ?>


            </div>
        </div>

    </div>

<?php $__env->stopSection(); ?>




<?php $__env->startSection('footer'); ?>


    <script>

        function DeletingModal(ID) {
            swal({
                title: "<?php echo e(trans('home.delete_msg1')); ?>",
                text: "<?php echo e(trans('home.delete_msg2')); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo e(trans('home.yes')); ?>",
                cancelButtonText: "<?php echo e(trans('home.no')); ?>",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    window.location.href = '<?php echo e(url('admin/delete-event-messages')); ?>' + '/' + ID + '/' + '<?php echo e($type); ?>';

                }
            });
        }


      function deleteToSelected() {
            swal({
                title: "هل تريد حقًا حذف الرسائل المختاره",
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
                  $('#delete-messages').attr('action', '<?php echo e(url("admin/delete-messages")); ?>').submit();
                }
              });
        }

    </script>


<?php $__env->stopSection(); ?>




<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/event_details/messages.blade.php ENDPATH**/ ?>