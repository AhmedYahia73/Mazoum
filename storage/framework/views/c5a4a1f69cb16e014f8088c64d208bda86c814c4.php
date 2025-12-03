<?php
    $lang = app()->getLocale();
?>


<div class="row justify-content-center">

    <div class="col-12">
        <div class="portfolio-list-box">
            <div class="portfoliolist" id="portfoliolist">

                <?php $__currentLoopData = $Desgins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="portfolio web-design" data-cat=".web-design">
                    <div class="portfolio-wrapper">
                        <div class="portfolio-img back-img" style="background-image: url('<?php echo e($item->file); ?>?<?php echo e(rand()); ?>')"></div>
                        <div class="portfolio-wrapper-text">
                            <h3 class="h3-title"> <?php echo e($item->{$lang.'_name'}); ?> </h3>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\wamp64\www\project-app\resources\views/user/includes/desgins.blade.php ENDPATH**/ ?>