@php
    $lang = app()->getLocale();
@endphp

<div class="pricing-list">
    <div class="row pricing-slider">

        @foreach ($Pricing as $item)
        <div class="col-lg-4">
            <div class="pricing-box wow fadeup-animation" data-wow-delay="0.4s">
                <div class="pricing-text">
                    <h3 class="h3-title">{{ $item->{$lang.'_title'} }}</h3>
                    <p> {{ $item->users_count }} </p>
                    <ul>
                      	@if($item->send_invitation == 'yes')
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                {{ $lang == 'en' ? 'send invitation' : 'أرسـال الدعوات ' }}
                        </li>
                      	@endif
                      
                      	@if($item->confirm_attendance == 'yes')
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                {{ $lang == 'en' ? 'confirm attendance' : 'تأكيد الحضـور ' }}
                        </li>
                      	@endif
                      
                      	@if($item->confirm_apology == 'yes')
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                {{ $lang == 'en' ? 'confirm apology' : 'تأكيد الاعتذار ' }}
                        </li>
                      	@endif
                      
                      
                      	@if($item->reminder_before_invitation == 'yes')
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                {{ $lang == 'en' ? 'reminder before invitation' : 'تذكير قبل الحفـل ' }}
                        </li>
                      	@endif
                      
                      	@if($item->party_employee == 'yes')
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                {{ $lang == 'en' ? 'party employee' : 'موظف للحفل' }}
                        </li>
                      	@endif
                      
                      	@if($item->attendance_report_after_invitation == 'yes')
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                {{ $lang == 'en' ? 'attendance report after invitation' : 'تقرير بالحضور بعد الحفل' }}
                        </li>
                      	@endif
                      
                      	@if($item->send_congratulations_after_invitation == 'yes')
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                {{ $lang == 'en' ? 'send congratulations after invitation' : 'إرسال تهنئة بعد الحفل' }}
                        </li>
                      	@endif
                      
                      	@if($item->congratulations_messages == 'yes')
                        <li>
                          		<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                {{ $lang == 'en' ? 'congratulations Messages' : ' رسائل تهنئة المناسبة ' }}
                        </li>
                      	@endif
                      
                    </ul>
                    <div class="pricing-rate">
                        <p><span style="font-size: 30px;"> {{ $item->price }} </span></p>
                    </div>
                    <a href="" class="sec-btn" title="{{ $lang == 'en' ? 'Join Now' : 'أنضم الان' }}">
                        <span>{{ $lang == 'en' ? 'Join Now' : 'أنضم الان' }}</span>
                    </a>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
