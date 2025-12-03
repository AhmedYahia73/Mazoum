<?php
    $lang = app()->getLocale();
?>


<div class="contact-form wow right-animation" data-wow-delay="0.4s">
    <form class="" action="<?php echo e(route($lang.'-save-contact-us')); ?>" method="post">

        <?php echo e(csrf_field()); ?>


        <div class="row">
            <div class="col-md-6">
                <div class="form-box">
                    <input name="first_name" value="<?php echo e(old('first_name')); ?>" type="text" required class="form-input" placeholder="<?php echo e($lang == 'en' ? 'First Name' : 'الأسم الأول'); ?>" required>
                    <?php if($errors->has('first_name')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('first_name')); ?> </strong>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-box">
                    <input name="last_name" value="<?php echo e(old('last_name')); ?>" type="text" class="form-input" placeholder="<?php echo e($lang == 'en' ? 'Last Name' : 'الأسم الأخير'); ?>" required>
                    <?php if($errors->has('last_name')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('last_name')); ?> </strong>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-box">
                    <input type="text" name="email" value="<?php echo e(old('email')); ?>" class="form-input" placeholder="<?php echo e($lang == 'en' ? 'Email Address' : 'البريد الألكتروني'); ?>" required>
                    <?php if($errors->has('email')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('email')); ?> </strong>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-box">
                    <input type="text" name="mobile" value="<?php echo e(old('mobile')); ?>" class="form-input" placeholder="<?php echo e($lang == 'en' ? 'Phone No.' : 'رقم الموبيل'); ?>" required>
                    <?php if($errors->has('mobile')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('mobile')); ?> </strong>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-12">
                <div class="form-box">
                    <textarea name="message" class="form-input" required placeholder="<?php echo e($lang == 'en' ? 'Message...' : 'الرسالة ...'); ?>"><?php echo e(old('message')); ?></textarea>
                    <?php if($errors->has('message')): ?>
                        <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('message')); ?> </strong>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-12">
                <div class="form-box">
                    <button name="submit" type="submit" value="Submit" class="sec-btn"><span> <?php echo e($lang == 'en' ? 'Submit Now' : 'أرسل الأن'); ?> </span></button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/includes/contact.blade.php ENDPATH**/ ?>