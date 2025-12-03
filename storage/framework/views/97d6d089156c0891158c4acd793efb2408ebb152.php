        <!--begin::Form-->
        

            <div class="card-body">

                <div class="row">


                    <div class="col-lg-6 col-md-6 <?php echo e($errors->has('name') ? ' has-error' : ''); ?>" style="margin-bottom: 20px">
                        <label> اسم المتسخدم </label>
                        <input type="text" disabled class="form-control m-input" value="<?php echo e(@$Item->user->name); ?>"  placeholder="عدد الدعوات ">
                        <?php if($errors->has('name')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('name')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-md-6 <?php echo e($errors->has('pass') ? ' has-error' : ''); ?>" style="margin-bottom: 20px">
                        <label> كلمه المرور </label>
                        <input type="text" disabled class="form-control m-input" value="<?php echo e(@$Item->user->pass); ?>"  placeholder="عدد الدعوات ">
                        <?php if($errors->has('pass')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('pass')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-6 col-md-6 <?php echo e($errors->has('invitation_count') ? ' has-error' : ''); ?>">
                        <label> عدد الدعوات </label>
                        <input type="text" name="invitation_count" disabled onkeypress="return isNumberKey(event)" class="form-control m-input" value="<?php echo e(@$Item->user->order->users_count); ?>"  placeholder="عدد الدعوات ">
                        <?php if($errors->has('invitation_count')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('invitation_count')); ?></strong>
                            </span>
                        <?php endif; ?>
                     </div>


                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('reservation_date') ? ' has-error' : ''); ?>">
                        <label> تاريخ  الدفع </label>
                        <input type="text" name="reservation_date" disabled class="form-control m-input" value="<?php echo e(@$Item->user->order->start_subscription_date); ?>"  placeholder="تاريخ  الحجز ">
                        <?php if($errors->has('reservation_date')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('reservation_date')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>


                    <div class="col-lg-4 col-md-4 <?php echo e($errors->has('package_price') ? ' has-error' : ''); ?>">
                        <label> سعر الباقة </label>
                        <input type="text" name="package_price" disabled class="form-control m-input" value="<?php echo e(@$Item->user->order->total); ?> <?php echo e(@$Item->user->order->currency->ar_name); ?>"  placeholder="سعر الباقة">
                        <?php if($errors->has('package_price')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('package_price')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-4 col-sm-4 <?php echo e($errors->has('payment_type') ? ' has-error' : ''); ?>">
                        <label> طريقة السداد <span class="text-danger">*</span>   </label>
                        <select name="payment_type" class="form-control" disabled>
                            <option value="" disabled selected> طريقة السداد  </option>
                            <option value="cash" <?php if(@$Item->user->order->payment_type == 'cash'): ?> selected <?php endif; ?>> كاش </option>
                            <option value="key_net" <?php if(@$Item->user->order->payment_type == 'key_net'): ?> selected <?php endif; ?>> كي نت </option>
                        </select>
                        <?php if($errors->has('payment_type')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('payment_type')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-4 col-sm-4 <?php echo e($errors->has('is_paid') ? ' has-error' : ''); ?>">
                        <label> طريقة الدفع <span class="text-danger">*</span>   </label>
                        <select name="is_paid" class="form-control" disabled>
                            <option value="" disabled selected> طريقة الدفع  </option>
                            <option value="paid" <?php if(@$Item->user->order->is_paid == 'paid'): ?> selected <?php endif; ?>> تم الدفع </option>
                            <option value="not_paid" <?php if(@$Item->user->order->is_paid == 'not_paid'): ?> selected <?php endif; ?>> لم يتم الدفع </option>
                        </select>
                        <?php if($errors->has('is_paid')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('is_paid')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>


                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('phone') ? ' has-error' : ''); ?>">
                        <label> رقم الهاتف </label>
                        <input type="text" name="phone" disabled class="form-control m-input" value="<?php echo e($Item->user->mobile_code); ?><?php echo e($Item->user->mobile); ?>"  placeholder="رقم الهاتف ">
                        <?php if($errors->has('phone')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('phone')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>


                    <div class="col-lg-6 col-sm-6 <?php echo e($errors->has('employee_gender') ? ' has-error' : ''); ?>">
                        <label> جنس الموظف   <span class="text-danger">*</span>   </label>
                        <input type="text" name="phone" disabled class="form-control m-input" value="<?php echo e($Item->user != null && $Item->user->employee_gender != null ? ($Item->user->employee_gender == 'male' ? 'رجل' : 'مرأة') : ''); ?>"  placeholder="جنس الموظف">
                        <?php if($errors->has('employee_gender')): ?>
                            <span class="help-block" style="color:red">
                                <strong><?php echo e($errors->first('employee_gender')); ?> </strong>
                            </span>
                        <?php endif; ?>
                    </div>



                </div>

            </div>

            

        
        <!--end::Form-->
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/events/my_package.blade.php ENDPATH**/ ?>