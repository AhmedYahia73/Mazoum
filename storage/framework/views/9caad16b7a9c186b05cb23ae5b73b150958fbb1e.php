<?php $__env->startSection('title','التصميمات'); ?>

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
      <a href="<?php echo e(asset('admin/desgins/create')); ?>" id="addRow" class="btn btn-primary">
        <i class="bx bx-plus"></i>&nbsp;
        أضافة تصميم جديد
      </a>
    </p>
  </div>
</div>



<!-- Zero configuration table -->
<section id="basic-datatable">

  <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            التصميمات
          </h4>
        </div>
        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> أسم التصميم </th>
                    <th> صوره / pdf </th>
                    <th> <?php echo e(trans('home.tools')); ?> </th>
                </tr>
              </thead>

              <tbody>

                <?php $x = 1; ?>

                <?php $__currentLoopData = $Item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                  <tr>
                      <td> <?php echo e($x); ?> </td>
                      <td>
                        <?php echo e($value->ar_name); ?>

                      </td>

                      <?php if($value->type == 'image'): ?>
                      <td>
                        <img src="<?php echo e($value->file); ?>?<?php echo e(rand()); ?>" style="width: 130px;display: block;margin: auto;">
                      </td>
                      <?php else: ?>
                      <td>
                        <a href="<?php echo e(asset('admin/desgins/show-pdf/'.$value->id)); ?>" target="_blank">
                            Open PDF
                        </a>
                      </td>
                      <?php endif; ?>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo e(url('admin/desgins/'). '/' . $value->id . '/edit'); ?>">
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
                    window.location.href = '<?php echo e(url('admin/desgins/destroy')); ?>' + '/' + ID;

                }
            });
        }

    </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/desgins/index.blade.php ENDPATH**/ ?>