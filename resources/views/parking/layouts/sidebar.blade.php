


    <li class="menu-item {{ count(Request::segments()) == 1 ? 'active' : '' }}">
        <a href="{{asset('parking_panel')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-calendar"></i>
            <div data-i18n="{{ trans('home.dashboard') }}">
                {{ trans('home.dashboard') }}
            </div>
        </a>
    </li>




    <li class="menu-item {{ request()->is('parking_panel/parking*') ? 'active' : '' }}">
        <a href="{{asset('parking_panel/parking')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="دخول باركينج">
            دخول باركينج
        </div>
        </a>
    </li>


    <li class="menu-item {{ request()->is('parking_panel/leave-parking*') ? 'active' : '' }}">
        <a href="{{asset('parking_panel/leave-parking')}}" class="menu-link" style="position:relative">
          <i class="menu-icon tf-icons bx bx-calendar"></i>
          <div data-i18n="خروج باركينج">
              خروج باركينج
          </div>
          <span style="position:absolute;left: 7px;background: red;width: 25px;height: 25px;line-height: 27px;text-align: center;border-radius: 50%;color: #FFF;">
           	{{ \App\Models\Parking::where('parking_status', 'leaving')->count() }}
          </span>
        </a>
    </li>

    <li class="menu-item {{ request()->is('parking_panel/reports*') ? 'active' : '' }}">
        <a href="{{asset('parking_panel/reports')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="التقارير">
            التقارير
        </div>
        </a>
    </li>



