<?php $__env->startSection('title',$title); ?>

<?php $__env->startSection('header'); ?>


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
          <b>
            <button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 0px;">
              حذف المستخدمين
            </button>
          </b>
        </div>
      
      	<?php echo Form::open(['role'=>'form','id'=>'all_users','method'=>'post']); ?>



        <input type="hidden" name="event_id" value="<?php echo e($Item->id); ?>">

        <div class="col-12">
            <div class="card"  style="padding: 20px">

                <h3>
                    <?php echo e($title); ?>

                </h3>

                <div>

                    <div class="table-responsive">

                        <table class="table zero-configuration">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">عدد الدعوات</th>
                                    <th scope="col"> أسم المستخدم </th>
                                    <th scope="col">رقم الهاتف </th>
                                    <th scope="col"  style="text-align: center;">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $x = 1; ?>

                                <?php if($data != null && $data->count() > 0): ?>

                                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <tr>
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
                                                <label for="user<?php echo e($user->id); ?>" style="cursor: pointer">
                                                    <?php echo e($user->name); ?>

                                                </label>
                                            </td>

                                            <td style="direction: ltr;">
                                                <?php echo e($user->mobile); ?>

                                            </td>

                                            <td style="text-align: center;">
                                                <?php echo e($user->status); ?>

                                            </td>


                                        </tr>

                                        <?php $x = $x + 1; ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php else: ?>
                                <tr>
                                    <td colspan="5">
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
                <button type="button" onclick="return sendToSelected();" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                    أرسال
                </button>
              	<button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 30px;">
                    حذف المستخدمين
                </button>
            </div>
        </div>

    <?php echo Form::close(); ?>


    </div>

<?php $__env->stopSection(); ?>





<?php $__env->startSection('footer'); ?>

    <script type="text/javascript">

        $(document).ready(function () {

            $('#select_all').click(function () {
               $('.check_items').prop('checked', this.checked);
            });
          
        });

    </script>

	<script>

        function sendToSelected() {
            swal({
                title: "هل تريد حقًا ارسال دعوه مره اخري",
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
                  $('#all_users').attr('action', '<?php echo e(url("assistant_panel/send_event_users")); ?>').submit();
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
                  $('#all_users').attr('action', '<?php echo e(url("assistant_panel/delete_event_users")); ?>').submit();
                }
              });
          }

    </script>

<?php $__env->stopSection(); ?>




<?php echo $__env->make('assistant.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/assistant/event_details/users.blade.php ENDPATH**/ ?>