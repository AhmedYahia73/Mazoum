<?php $__env->startSection('title','الأحداث'); ?>

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



<!-- Zero configuration table -->
<section id="basic-datatable">

  <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            الأحداث
          </h4>
        </div>
        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> أسم الحدث </th>
                    <th> موقع الحدث </th>
                    <th> تاريخ الحدث </th>
                    <th> أسم المستخدم </th>
                    <th> صوره  </th>
                    <th> <?php echo e(trans('home.tools')); ?> </th>
                </tr>
              </thead>

              <tbody>

                <?php $x = 1; ?>

                <?php $__currentLoopData = $Item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                  <tr>
                      <td> <?php echo e($x); ?> </td>
                      <td> <?php echo e($value->title); ?> </td>

                      <td> <?php echo e($value->address); ?> </td>
                      <td> <?php echo e($value->date); ?> </td>
                      <td> <?php echo e($value->user != null ? $value->user->name : ''); ?> </td>
                      <td> <img src="<?php echo e($value->file); ?>?<?php echo e(rand()); ?>" style="width: 130px;display: block;margin: auto;"> </td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo e(url('assistant_panel/event-details/'). '/' . $value->id); ?>">
                                <i class="bx bx-eye-alt me-1"></i> التفاصيل
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
                    window.location.href = '<?php echo e(url('assistant_panel/events/destroy')); ?>' + '/' + ID;

                }
            });
        }

    </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('assistant.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/assistant/events/index.blade.php ENDPATH**/ ?>