<?php
    $lang = app()->getLocale();
?>

<div class="pricing-list">
    <div class="row pricing-slider">

        <?php $__currentLoopData = $Pricing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-4">
            <div class="pricing-box wow fadeup-animation" data-wow-delay="0.4s">
                <div class="pricing-text">
                    <h3 class="h3-title"><?php echo e($item->{$lang.'_title'}); ?></h3>
                    <p> <?php echo e($item->users_count); ?> </p>
                    <ul>
                      	<?php if($item->send_invitation == 'yes'): ?>
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <?php echo e($lang == 'en' ? 'send invitation' : 'أرسـال الدعوات '); ?>

                        </li>
                      	<?php endif; ?>
                      
                      	<?php if($item->confirm_attendance == 'yes'): ?>
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <?php echo e($lang == 'en' ? 'confirm attendance' : 'تأكيد الحضـور '); ?>

                        </li>
                      	<?php endif; ?>
                      
                      	<?php if($item->confirm_apology == 'yes'): ?>
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <?php echo e($lang == 'en' ? 'confirm apology' : 'تأكيد الاعتذار '); ?>

                        </li>
                      	<?php endif; ?>
                      
                      
                      	<?php if($item->reminder_before_invitation == 'yes'): ?>
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <?php echo e($lang == 'en' ? 'reminder before invitation' : 'تذكير قبل الحفـل '); ?>

                        </li>
                      	<?php endif; ?>
                      
                      	<?php if($item->party_employee == 'yes'): ?>
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <?php echo e($lang == 'en' ? 'party employee' : 'موظف للحفل'); ?>

                        </li>
                      	<?php endif; ?>
                      
                      	<?php if($item->attendance_report_after_invitation == 'yes'): ?>
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <?php echo e($lang == 'en' ? 'attendance report after invitation' : 'تقرير بالحضور بعد الحفل'); ?>

                        </li>
                      	<?php endif; ?>
                      
                      	<?php if($item->send_congratulations_after_invitation == 'yes'): ?>
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <?php echo e($lang == 'en' ? 'send congratulations after invitation' : 'إرسال تهنئة بعد الحفل'); ?>

                        </li>
                      	<?php endif; ?>
                      
                      	<?php if($item->congratulations_messages == 'yes'): ?>
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <?php echo e($lang == 'en' ? 'congratulations Messages' : ' رسائل تهنئة المناسبة '); ?>

                        </li>
                      	<?php endif; ?>
                      
                    </ul>
                    <div class="pricing-rate">
                        <p><span style="font-size: 30px;"> <?php echo e($item->price); ?> </span></p>
                    </div>
                    <a href="" class="sec-btn" title="<?php echo e($lang == 'en' ? 'Join Now' : 'أنضم الان'); ?>">
                        <span><?php echo e($lang == 'en' ? 'Join Now' : 'أنضم الان'); ?></span>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
</div>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/includes/pricing.blade.php ENDPATH**/ ?>