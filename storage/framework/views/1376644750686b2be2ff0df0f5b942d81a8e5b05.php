<?php $__env->startSection('title','دعوات خاصة'); ?>

<?php $__env->startSection('header'); ?>

  <style>
    th , td {
      text-align: center !important
    }

    .is_popularity i {
        color: blue
    }
  </style>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>

<div class="row">
  <div class="col-12">
    <p>
      <a href="<?php echo e(asset('admin/custom_events/create')); ?>" id="addRow" class="btn btn-primary">
        <i class="bx bx-plus"></i>&nbsp;
        أضافه دعوه جديدة
      </a>
    </p>
  </div>
</div>



<!-- Zero configuration table -->
<section id="basic-datatable">

  <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-12">

      <?php echo Form::open(['url' => "admin/delete_selected_custom_events", 'role'=>'form','id'=>'delete_selected_items','method'=>'post']); ?>


      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
           دعوات خاصة
          </h4>
        </div>

        

        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> العنوان  </th>
                    <th> المستخدم </th>
                    <th>  الصورة </th>
                    <th> <?php echo e(trans('home.tools')); ?>  </th>
                </tr>
              </thead>

              <tbody>

                <?php $x = 1; ?>

                <?php $__currentLoopData = $Item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                  <tr>
                    <td>
                      <span> <?php echo e($x); ?> </span>
                      
                    </td>
                    <td> <?php echo e($value->title); ?> </td>
                    <td> <?php echo e($value->user != null ? $value->user->name : ''); ?> </td>
                    <td>
                        <img src="<?php echo e($value->image); ?>?<?php echo e(rand()); ?>" style="width: 150px">
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo e(url('admin/custom_events/'). '/' . $value->id . '/edit'); ?>">
                                <i class="bx bx-edit-alt me-1"></i> <?php echo e(trans('home.Edit')); ?>

                            </a>
                            <a onclick="return DeletingModal(<?php echo e($value->id); ?>);" class="dropdown-item DeletingModal" name="<?php echo e($value->id); ?>" href="javascript:void(0);">
                                <i class="bx bx-trash me-1"></i> <?php echo e(trans('home.Delete')); ?>

                            </a>
                            </div>
                        </div>
                    </td>
                  </tr>

                  <?php $x = $x + 1; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


              </tbody>

            </table>
          </div>
        </div>
      </div>

      <?php echo Form::close(); ?>



    </div>
  </div>
</section>
<!--/ Zero configuration table -->



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
                window.location.href = '<?php echo e(url('admin/custom_events/destroy')); ?>' + '/' + ID;
            }
        });
    }

  </script>


	<script>

        $(document).ready(function () {

            $('#select_all').click(function () {
               $('.check_items').prop('checked', this.checked);
            });

            $('#delete_selected_items_btn').click(function() {

                swal({
                        title: "هل تريد حذف الصفوف المختارة ",
                        text: "بعد الحذف لا يمكنك العودة.",
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
                            $('#delete_selected_items').submit();
                        }
                    }
                );
            })

        });

    </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/custom_events/index.blade.php ENDPATH**/ ?>