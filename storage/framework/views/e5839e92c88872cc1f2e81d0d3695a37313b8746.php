<?php $__env->startSection('title',trans('home.users')); ?>

<?php $__env->startSection('header'); ?>

  <style>
    th , td {
      text-align: center !important
    }

    .is_popularity i {
        color: blue
    }

    [dir=rtl] table.dataTable thead th, [dir=rtl] table.dataTable tbody td, [dir=rtl] table.dataTable tfoot th {
        padding-right: 15px;
        padding-left: 15px;
    }
  </style>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>

<div class="row">
  <div class="col-12">
    <p>
      <a href="<?php echo e(asset('admin/users/create')); ?>" id="addRow" class="btn btn-primary">
        <i class="bx bx-plus"></i>&nbsp;
        <?php echo e(trans('home.add_user')); ?>

      </a>
    </p>
  </div>
</div>



<!-- Zero configuration table -->
<section id="basic-datatable">

  <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-12">

      <?php echo Form::open(['url' => "admin/delete_selected_users", 'role'=>'form','id'=>'delete_selected_items','method'=>'post']); ?>


      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            <?php echo e(trans('home.users')); ?>

          </h4>
        </div>

        <div class="card-header" style="margin-bottom:20px">
          <b>
            <label class="btn btn-primary">
              <input type="checkbox" for="checkbox_select" id="select_all" name="select_all">
              <span>
                أختيار الكل
              </span>
            </label>
          </b>

          <button type="button" id="delete_selected_items_btn" class="btn btn-danger mr-2">
            حذف العناصر المختارة
          </button>

        </div>

        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th style="text-align: right !important">  <?php echo e(trans('home.name')); ?>  </th>
                    <th> تاريخ الأنشاء </th>
                    <th>  <?php echo e(trans('home.mobile')); ?>  </th>
                    <th>  الباقة الحالية </th>
                    <th>  الرصيد </th>
                    <th> <?php echo e(trans('home.tools')); ?>  </th>
                </tr>
              </thead>

              <tbody>

                <?php $x = 1; ?>

                <?php $__currentLoopData = $Item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                  <tr>
                    <td>
                      <span style="font-size: 18px;"> <?php echo e($x); ?> </span>
                      <input class="check_items" type="checkbox" value="<?php echo e($value->id); ?>" name="items[]" style="width: 17px;height: 17px;" />
                    </td>

                    <td style="text-align: right !important"> <?php echo e($value->name); ?> </td>

                    <td> <?php echo e(Carbon\Carbon::parse($value->created_at)->format('Y-m-d h:i A')); ?> </td>

                    <td style="direction: ltr;">
                        <?php echo e($value->mobile_code); ?>

                        <?php echo e($value->mobile); ?>

                    </td>

                    <td> <?php echo e($value->offer != null ? $value->offer->ar_name : ''); ?> </td>

                    <td> <?php echo e($value->balance); ?> </td>

                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo e(url('admin/users/'). '/' . $value->id . '/edit'); ?>">
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
                window.location.href = '<?php echo e(url('admin/users/destroy')); ?>' + '/' + ID;
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

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/users/index.blade.php ENDPATH**/ ?>