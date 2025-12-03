<?php
     $user_events = App\Models\EventUsers::where('event_id',$Item->id)->where('scan','!=',null)->get();
?>

<div class="row">
    <div class="col-lg-12">

        <table class="table" id="event_users_table">
            <thead>
                <tr>
                    <th scope="col"> م </th>
                    <th scope="col">  أسم المستخدم </th>
                    <th scope="col"> عدد الحضور </th>
                    <th scope="col"> رقم الهاتف </th>
                    <th scope="col"> حالة الحضور </th>
                    <th scope="col"> وقت الحضور </th>
                    <th scope="col">  مسح QR </th>
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
                            <?php echo e($user_event->users_count); ?>

                        </td>
                        <td style="color: #000">
                            <?php echo e($user_event->mobile); ?>

                        </td>
                        <td style="color: #000">
                            <?php if($user_event->status == 'attend'): ?>
                                اكد الحضور
                            <?php endif; ?>
                            <?php if($user_event->status == 'not-attend'): ?>
                                اعتذر عن الحضور
                            <?php endif; ?>
                            <?php if($user_event->status == 'hold'): ?>
                                لم يرسل دعوه بعد
                            <?php endif; ?>
                        </td>

                        <td style="color: #000">
							<?php if(! empty($user_event->scan_at) && is_array($user_event->scan_at)): ?>
                          		<?php $__currentLoopData = $user_event->scan_at; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          			<span style="font-size: 14px;display: block;">
                                      <?php echo e(Carbon\Carbon::parse($date)->format('Y-m-d h:i:s A')); ?>

                          			</span>
                          		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          	<?php else: ?>
                             <?php echo e($user_event->scan_at != null ? Carbon\Carbon::parse($user_event->scan_at)->format('Y-m-d h:i:s A') : ''); ?>

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



<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/events/event_users.blade.php ENDPATH**/ ?>