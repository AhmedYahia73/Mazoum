


    <li class="menu-item <?php echo e(count(Request::segments()) == 1 ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin')); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx bx-calendar"></i>
            <div data-i18n="<?php echo e(trans('home.dashboard')); ?>">
                <?php echo e(trans('home.dashboard')); ?>

            </div>
        </a>
    </li>


    <li class="menu-item <?php echo e(request()->is('admin/admin*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/manager')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="<?php echo e(trans('home.managers')); ?>">
            <?php echo e(trans('home.managers')); ?>

        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/currency*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/currency')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="العملات">
            العملات
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/mobile_codes*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/mobile_codes')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="أكواد الموبيل">
            أكواد الموبيل
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/users*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/users')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="<?php echo e(trans('home.users')); ?>">
            <?php echo e(trans('home.users')); ?>

        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/assistant*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/assistant')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="الموظفين">
            الموظفين
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/setting*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/setting')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="<?php echo e(trans('home.setting')); ?>">
            <?php echo e(trans('home.setting')); ?>

        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/uses*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/uses')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="كيفية الأستخدام">
            كيفية الأستخدام
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/packages*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/packages')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="الباقات">
            الباقات
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/reservation*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/reservation')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="حجز باقات معزوم">
            حجز باقات معزوم
        </div>
        </a>
    </li>


    <li class="menu-item <?php echo e(request()->is('admin/pricing*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/pricing')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="باقات الموقع">
            باقات الموقع
        </div>
        </a>
    </li>


    <li class="menu-item <?php echo e(request()->is('admin/desgins*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/desgins')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="تصميمات التطبيق">
            تصميمات التطبيق
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/web_desgins*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/web_desgins')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="تصميمات الموقع">
            تصميمات الموقع
        </div>
        </a>
    </li>





    <li class="menu-item <?php echo e(request()->is('admin/events') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/events')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="مناسبات الكويت">
            مناسبات الكويت
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/sa-events*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/sa-events')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="مناسبات السعودية">
            مناسبات السعودية
        </div>
        </a>
    </li>


    <li class="menu-item <?php echo e(request()->is('admin/custom_events*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/custom_events')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="دعوات خاصة">
            دعوات خاصة
        </div>
        </a>
    </li>



    <li class="menu-item <?php echo e(request()->is('admin/messages*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/messages')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="الرسائل">
            الرسائل
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('admin/subscribers*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('admin/subscribers')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="الأشتركات">
            الأشتركات
        </div>
        </a>
    </li>




<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/layouts/sidebar.blade.php ENDPATH**/ ?>