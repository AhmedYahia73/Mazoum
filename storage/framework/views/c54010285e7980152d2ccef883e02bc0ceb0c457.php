


    <li class="menu-item <?php echo e(count(Request::segments()) == 1 ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('assistant_panel')); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx bx-calendar"></i>
            <div data-i18n="لوحة التحكم">
                لوحة التحكم
            </div>
        </a>
    </li>


    <li class="menu-item <?php echo e(request()->is('assistant_panel/profile') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('assistant_panel/profile')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="الملف الشخصي">
            الملف الشخصي
        </div>
        </a>
    </li>



    <li class="menu-item <?php echo e(request()->is('assistant_panel/events*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('assistant_panel/events')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="الأحداث">
            الأحداث
        </div>
        </a>
    </li>

    <li class="menu-item <?php echo e(request()->is('assistant_panel/custom_events*') ? 'active' : ''); ?>">
        <a href="<?php echo e(asset('assistant_panel/custom_events')); ?>" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="دعوات خاصة">
            دعوات خاصة
        </div>
        </a>
    </li>



<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/assistant/layouts/sidebar.blade.php ENDPATH**/ ?>