


    <button type="button" class="btn btn-warning" style="margin-top: 20px;margin-bottom: 10px" data-bs-toggle="modal" data-bs-target="#exampleModal">
        أشترك في باقة جديدة
    </button>


    <div class="table-responsive">
        <table class="table zero-configuration">

            <thead>
                <tr>
                    <th> رقم الطلب </th>
                    <th> الباقة </th>
                    <th> السعر </th>
                    <th> عدد المستخدمين </th>
                    <th> تاريخ انشاء الأشتراك </th>
                    <th> تاريخ بدايه الأشتراك </th>
                    <th> مده الأشتراك </th>
                    <th> طريقة السداد </th>
                    <th> طريقة الدفع </th>
                    <th> جنس الموظف  </th>
                    <th> الأدوات </th>
                </tr>
            </thead>

            <tbody>

                <?php $x = 1; ?>

                <?php if($Item->orders != null && $Item->orders()->count() > 0): ?>

                    <?php $__currentLoopData = $Item->orders()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <tr id="row<?php echo e($value->id); ?>">
                            <td>  <?php echo e($value->order_number); ?> </td>
                            <td>
                                <?php if($value->type == 'offer'): ?>
                                    <?php echo e($value->offer != null ? $value->offer->ar_name : ''); ?>

                                <?php else: ?>
                                    أشتراك يدوي
                                <?php endif; ?>
                            </td>
                            <td> <?php echo e($value->total); ?> <?php echo e($value->currency != null ? $value->currency->ar_name : ''); ?> </td>
                            <td> <?php echo e($value->users_count); ?> </td>
                            <td> <?php echo e($value->operation_date); ?> </td>
                            <td> <?php echo e($value->start_subscription_date); ?> </td>
                            <td>
                                <?php if($value->duration_type == 'day'): ?>
                                    <?php echo e($value->duration); ?> يوم
                                <?php elseif($value->duration_type == 'month'): ?>
                                    <?php echo e($value->duration); ?> شهر
                                <?php elseif($value->duration_type == 'year'): ?>
                                    <?php echo e($value->duration); ?> سنه
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($value->payment_type == 'cash'): ?>
                                    كاش
                                <?php elseif($value->payment_type == 'key_net'): ?>
                                    كي نت
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($value->is_paid == 'paid'): ?>
                                    تم الدفع
                                <?php elseif($value->is_paid == 'not_paid'): ?>
                                    لم يتم الدفع
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($value->employee_gender == 'male'): ?>
                                    رجل
                                <?php elseif($value->employee_gender == 'female'): ?>
                                    مرأه
                                <?php endif; ?>
                            </td>
                            <td>
                                <a onclick="return DeletingModal(<?php echo e($value->id); ?>);" class="dropdown-item DeletingModal" name="<?php echo e($value->id); ?>" href="javascript:void(0);">
                                  <i class="bx bx-trash me-1"></i> <?php echo e(trans('home.Delete')); ?>

                                </a>
                                <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editOrderModal<?php echo e($value->id); ?>">
                                    <i class="bx bx-edit-alt me-1"></i> <?php echo e(trans('home.Edit')); ?>

                                </a>
                            </td>
                        </tr>

                        <?php $x = $x + 1; ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            عفوا لا يوجد طلب
                        </td>
                    </tr>
                <?php endif; ?>


            </tbody>

        </table>
    </div>




    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">أشترك في باقة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <?php echo Form::open(['url' => "admin/save-order", 'role'=>'form','id'=>'save-order','method'=>'post']); ?>


                        <input type="hidden" name="user_id" value="<?php echo e($Item->id); ?>">

                        <div class="mb-3">
                            <label> نوع الأشترك </label>
                            <select name="type" id="order_type" class="form-control" required>
                                <option value="" disabled selected> نوع الأشتراك </option>
                                <option value="offer" <?php if(old('type') == 'offer'): ?> selected <?php endif; ?>>
                                    باقات حالية
                                </option>
                                <option value="fixed-price" <?php if(old('type') == 'fixed-price'): ?> selected <?php endif; ?>>
                                    سعر ثابت
                                </option>
                            </select>
                            <?php if($errors->has('type')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('type')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php
                            $offers = App\Models\Packages::where('status',1)->get();
                        ?>

                        <div class="mb-3 offers un_active">
                           <label> الباقات </label>
                            <select name="offer_id" class="form-control">
                                <option value="" disabled selected>  أختر باقة </option>
                                <?php $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($offer->id); ?>" <?php if(old('offer_id') == $offer->id): ?> selected <?php endif; ?>>
                                    <?php echo e($offer->ar_name); ?> - <?php echo e($offer->price); ?> <?php echo e($offer->currency != null ? $offer->currency->ar_name : ''); ?> - <?php echo e($offer->users_count); ?> مستخدم
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($errors->has('offer_id')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('offer_id')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3 users_count un_active">
                           <label> عدد المستخدمين </label>
                           <input type="number" name="users_count" class="form-control" value="<?php echo e(old('users_count')); ?>" min="1" placeholder="عدد المستخدمين">
                           <?php if($errors->has('users_count')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('users_count')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3 total_price un_active">
                           <label> السعر </label>
                           <input type="number" name="total" class="form-control" value="<?php echo e(old('total')); ?>" placeholder="السعر">
                           <?php if($errors->has('total')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('total')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3 currencies un_active">
                           <label> العملات </label>
                            <select name="currency_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
                                <option value="" disabled selected>  أختر عملة </option>
                                <?php $__currentLoopData = Currencies(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php if(old('currency_id') == $key): ?> selected <?php endif; ?>>
                                        <?php echo e($value); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($errors->has('currency_id')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('currency_id')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3 <?php echo e($errors->has('start_subscription_date') ? ' has-error' : ''); ?>">
                            <label> تاريخ الأشتراك <span class="text-danger">*</span> </label>
                            <input type="text" name="start_subscription_date" required class="form-control m-input" value="<?php echo e(old('start_subscription_date')); ?>"  placeholder="YYYY-MM-DD" id="flatpickr-date">
                            <?php if($errors->has('start_subscription_date')): ?>
                                 <span class="help-block" style="color:red">
                                      <strong><?php echo e($errors->first('start_subscription_date')); ?> </strong>
                                 </span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label> نوع المده </label>
                             <select name="duration_type" required class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
                                <option value="" disabled selected>  أختر نوع </option>
                                <option value="day" <?php if(old('duration_type') == 'day'): ?> selected <?php endif; ?>> يوم </option>
                                <option value="month" <?php if(old('duration_type') == 'month'): ?> selected <?php endif; ?>> شهر </option>
                                <option value="year" <?php if(old('duration_type') == 'year'): ?> selected <?php endif; ?>> سنه </option>
                             </select>
                             <?php if($errors->has('duration_type')): ?>
                                 <span class="help-block" style="color:red">
                                     <strong><?php echo e($errors->first('duration_type')); ?> </strong>
                                 </span>
                             <?php endif; ?>
                         </div>

                        <div class="mb-3">
                            <label> المده </label>
                            <input type="number" name="duration" class="form-control" value="<?php echo e(old('duration')); ?>" required placeholder="المده">
                            <?php if($errors->has('duration')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('duration')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label> طريقة السداد <span class="text-danger">*</span>   </label>
                            <select name="payment_type" class="form-control m-bootstrap-select m_selectpicker" required>
                                <option value="" disabled selected>  أختر النوع </option>
                                <option value="cash" <?php if(old('payment_type') == 'cash'): ?> selected <?php endif; ?>> كاش </option>
                                <option value="key_net" <?php if(old('payment_type') == 'key_net'): ?> selected <?php endif; ?>> كي نت </option>
                            </select>
                            <?php if($errors->has('payment_type')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('payment_type')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label> جنس الموظف   <span class="text-danger">*</span>   </label>
                            <select name="employee_gender" class="form-control m-bootstrap-select m_selectpicker" required>
                                <option value="" disabled selected>  أختر النوع </option>
                                <option value="male" <?php if(old('employee_gender') == 'male'): ?> selected <?php endif; ?>> رجل </option>
                                <option value="female" <?php if(old('employee_gender') == 'female'): ?> selected <?php endif; ?>> مرأه  </option>
                            </select>
                            <?php if($errors->has('employee_gender')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('employee_gender')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label> طريقة الدفع <span class="text-danger">*</span>   </label>
                            <select name="is_paid" class="form-control m-bootstrap-select m_selectpicker" required>
                                <option value="" disabled selected>  أختر النوع </option>
                                <option value="paid" <?php if(old('is_paid') == 'paid'): ?> selected <?php endif; ?>> تم الدفع </option>
                                <option value="not_paid" <?php if(old('is_paid') == 'not_paid'): ?> selected <?php endif; ?>> لم يتم الدفع </option>
                            </select>
                            <?php if($errors->has('is_paid')): ?>
                                <span class="help-block" style="color:red">
                                    <strong><?php echo e($errors->first('is_paid')); ?> </strong>
                                </span>
                            <?php endif; ?>
                        </div>

                    <?php echo Form::close(); ?>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        أغلاق
                    </button>
                    <button type="submit" form="save-order" class="btn btn-primary">
                        أشترك
                    </button>
                </div>

            </div>
        </div>
    </div>


    <?php if($Item->orders != null && $Item->orders()->count() > 0): ?>

        <?php $__currentLoopData = $Item->orders()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="editOrderModal<?php echo e($order->id); ?>" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrderModalLabel"> تعديل بيانات الباقة </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <?php echo Form::open(['url' => "admin/edit-order", 'role'=>'form','id'=>'edit-order'.$order->id,'method'=>'post']); ?>


                            <input type="hidden" name="order_id" value="<?php echo e($order->id); ?>">

                            <div class="mb-3 <?php echo e($errors->has('start_subscription_date') ? ' has-error' : ''); ?>">
                                <label> تاريخ الأشتراك <span class="text-danger">*</span> </label>
                                <input type="text" name="start_subscription_date" required class="form-control m-input flatpickr-date" value="<?php echo e($order->start_subscription_date); ?>"  placeholder="YYYY-MM-DD">
                                <?php if($errors->has('start_subscription_date')): ?>
                                    <span class="help-block" style="color:red">
                                        <strong><?php echo e($errors->first('start_subscription_date')); ?> </strong>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label> نوع المده </label>
                                <select name="duration_type" required class="form-control m-bootstrap-select m_selectpicker" data-live-search="true">
                                    <option value="" disabled selected>  أختر نوع </option>
                                    <option value="day" <?php if($order->duration_type == 'day'): ?> selected <?php endif; ?>> يوم </option>
                                    <option value="month" <?php if($order->duration_type == 'month'): ?> selected <?php endif; ?>> شهر </option>
                                    <option value="year" <?php if($order->duration_type == 'year'): ?> selected <?php endif; ?>> سنه </option>
                                </select>
                                <?php if($errors->has('duration_type')): ?>
                                    <span class="help-block" style="color:red">
                                        <strong><?php echo e($errors->first('duration_type')); ?> </strong>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label> المده </label>
                                <input type="number" name="duration" class="form-control" value="<?php echo e($order->duration); ?>" required placeholder="المده">
                                <?php if($errors->has('duration')): ?>
                                    <span class="help-block" style="color:red">
                                        <strong><?php echo e($errors->first('duration')); ?> </strong>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label> طريقة السداد <span class="text-danger">*</span>   </label>
                                <select name="payment_type" class="form-control m-bootstrap-select m_selectpicker" required>
                                    <option value="" disabled selected>  أختر النوع </option>
                                    <option value="cash" <?php if($order->payment_type == 'cash'): ?> selected <?php endif; ?>> كاش </option>
                                    <option value="key_net" <?php if($order->payment_type == 'key_net'): ?> selected <?php endif; ?>> كي نت </option>
                                </select>
                                <?php if($errors->has('payment_type')): ?>
                                    <span class="help-block" style="color:red">
                                        <strong><?php echo e($errors->first('payment_type')); ?> </strong>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label> جنس الموظف   <span class="text-danger">*</span>   </label>
                                <select name="employee_gender" class="form-control m-bootstrap-select m_selectpicker" required>
                                    <option value="" disabled selected>  أختر النوع </option>
                                    <option value="male" <?php if($order->employee_gender == 'male'): ?> selected <?php endif; ?>> رجل </option>
                                    <option value="female" <?php if($order->employee_gender == 'female'): ?> selected <?php endif; ?>> مرأه  </option>
                                </select>
                                <?php if($errors->has('employee_gender')): ?>
                                    <span class="help-block" style="color:red">
                                        <strong><?php echo e($errors->first('employee_gender')); ?> </strong>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label> طريقة الدفع <span class="text-danger">*</span>   </label>
                                <select name="is_paid" class="form-control m-bootstrap-select m_selectpicker" required>
                                    <option value="" disabled selected>  أختر النوع </option>
                                    <option value="paid" <?php if($order->is_paid == 'paid'): ?> selected <?php endif; ?>> تم الدفع </option>
                                    <option value="not_paid" <?php if($order->is_paid == 'not_paid'): ?> selected <?php endif; ?>> لم يتم الدفع </option>
                                </select>
                                <?php if($errors->has('is_paid')): ?>
                                    <span class="help-block" style="color:red">
                                        <strong><?php echo e($errors->first('is_paid')); ?> </strong>
                                    </span>
                                <?php endif; ?>
                            </div>

                        <?php echo Form::close(); ?>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            أغلاق
                        </button>
                        <button type="submit" form="edit-order<?php echo e($order->id); ?>" class="btn btn-primary">
                            تعديل
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php endif; ?>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/users/user-invoices.blade.php ENDPATH**/ ?>