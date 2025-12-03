<?php
     $user_events = App\Models\CustomEventUsers::where('custom_event_id',$Item->id)->get();
?>

<div class="row">
    <div class="col-lg-12">

        

        <table class="table" id="event_users_table" style="table-layout: fixed">
            <thead>
                <tr>
                    <th> م </th>
                    <th>  أسم المستخدم </th>
                    <th> رقم الهاتف </th>
                    <th> عدد الحضور </th>
                    <th> حالة الحضور </th>
                    <th> وقت الحضور </th>
                    <th>  مسح QR </th>
                </tr>
            </thead>
            <tbody>
                <?php $x = 1; ?>

                <?php $__currentLoopData = $user_events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user_event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <tr>
                        <td style="color: #000">
                            <?php echo e($x); ?>

                        </td>

                        <td style="color: #000">
                            <?php echo e($user_event->name); ?>

                        </td>

                        <td style="color: #000">
                            <?php echo e($user_event->mobile); ?>

                        </td>

                      	<td style="color: #000">
                            <?php echo e($user_event->users_count); ?>

                        </td>


                        <td style="color: #000">
							اكد الحضور <?php echo e($user_event->scan_count); ?> / <?php echo e($user_event->users_count); ?>

                        </td>

                        <td style="color: #000">
							<?php if(! empty($user_event->scan_at)): ?>
                          		<?php $__currentLoopData = $user_event->scan_at; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          			<span style="font-size: 14px;display: block;">
                                      <?php echo e(Carbon\Carbon::parse($date)->format('Y-m-d h:i A')); ?>

                          			</span>
                          		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          	<?php endif; ?>
                        </td>

                        <td style="color: #000">
							<?php echo e($user_event->scan_count); ?> / <?php echo e($user_event->users_count); ?>

                        </td>

                    </tr>

                    <?php $x = $x + 1; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </tbody>
        </table>
    </div>
</div>



<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/custom_events/event_users.blade.php ENDPATH**/ ?>