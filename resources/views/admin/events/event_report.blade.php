@php
    $mobiles = App\Models\EventUsers::where('event_id',$Item->id)->pluck('mobile')->toArray();

    $mobiles_arr = [];

    foreach($mobiles as $phone) {
        $mobiles_arr[] = ltrim($phone,"+");
    }

    $messages_count = App\Models\CongratulationMessages::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->whereIn('mobile',$mobiles_arr)->count();

@endphp




<div class="row">

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventMessages::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->whereIn('mobile',$mobiles_arr)->count() }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/event-messages/'.$Item->id) }}">
                        رسائل الاعتذار
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUsers::where('event_id',$Item->id)->sum('users_count') }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/all-invited-users/'.$Item->id) }}">
                         المدعوين
                    </a>
                </span>
            </div>
        </div>
    </div>


    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUsers::where('event_id',$Item->id)->where('scan','yes')->sum('scan_count') }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/event-qr-details/'.$Item->id) }}">
                        QR
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUsers::where('event_id',$Item->id)->where('is_accepted','yes')->sum('users_count') }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/confirmed-event-details/'.$Item->id) }}">
                        تأكيد الحضور
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUsers::where('event_id',$Item->id)->where('status','not-attend')->sum('users_count') }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/not-attend-event-details/'.$Item->id) }}">
                        الأعتذار
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUsers::where('event_id',$Item->id)->where('status','hold')->where('is_new_sent',0)->whereNull('is_sent')->sum('users_count') }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/hold-event-details/'.$Item->id) }}">
                        بأنتظار الأرسال
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUsers::where('event_id',$Item->id)->whereIn('status', ['sent'])->whereNull('is_accepted')->whereNull('is_refused')->where(function($query) { $query->where('is_new_sent',1)->orWhereNotNull('is_sent'); })->sum('users_count') }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/failed-event-details/'.$Item->id) }}">
                      لم يتم التاكيد
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUsers::where('event_id',$Item->id)->where('qr_sent','yes')->sum('users_count') }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/qr-sent-event-details/'.$Item->id) }}">
                        Sent QR
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    0
                </h2>
                <span class="text-muted">
                    <a href="">
                        تذكير
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ $messages_count }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/congratulations-event-messages-details/'.$Item->id) }}">
                        رسائل التهنئة
                    </a>
                </span>
            </div>
        </div>
    </div>



  <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUsers::where('event_id',$Item->id)->where('status','attend')->whereNull('scan')->whereNull('is_refused')->sum('users_count') }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/non-attendance-event-details/'.$Item->id) }}">
                    	عدم الحضور
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\EventUserActions::where('event_id',$Item->id)->where('action','accept_event')->count() }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin/confirmed-users-web-chat/'.$Item->id) }}">
                    	تاكيد مستخدمين الويب
                    </a>
                </span>
            </div>
        </div>
    </div>



</div>


