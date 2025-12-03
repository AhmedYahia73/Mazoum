<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>


<?php if($message = Session::get('success')): ?>
<div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
    <?php echo e($message); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>


<?php if($message = Session::get('error')): ?>
<div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
    <?php echo e($message); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if($message = Session::get('error_message')): ?>
<div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
    <?php echo e($message); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>



<?php if($message = Session::get('warning')): ?>
<div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
    <?php echo e($message); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>


<?php if($message = Session::get('info')): ?>
<div class="alert alert-info alert-dismissible d-flex align-items-center" role="alert">
    <?php echo e($message); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/flash-message.blade.php ENDPATH**/ ?>