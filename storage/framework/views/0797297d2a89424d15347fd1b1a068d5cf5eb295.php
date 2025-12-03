<?php
    $mobiles = App\Models\EventUsers::where('event_id',$Item->id)->pluck('mobile')->toArray();

    $mobiles_arr = [];

    foreach($mobiles as $phone) {
        $mobiles_arr[] = ltrim($phone,"+");
    }

    $messages_count = App\Models\CongratulationMessages::whereHas('event',function($event) { $event->where('is_open','yes'); })->whereIn('mobile',$mobiles_arr)->count();

?>




<div class="row">

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    <?php echo e(App\Models\EventMessages::whereHas('event',function($event) { $event->where('is_open','yes'); })->whereIn('mobile',$mobiles_arr)->count()); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/event-messages/'.$Item->id)); ?>">
                        الرسائل
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    <?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->sum('users_count')); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/all-invited-users/'.$Item->id)); ?>">
                        ضيوف المناسبة
                    </a>
                </span>
            </div>
        </div>
    </div>


    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    <?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->where('scan','yes')->sum('scan_count')); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/event-qr-details/'.$Item->id)); ?>">
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
                    <?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->where('is_accepted','yes')->sum('users_count')); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/confirmed-event-details/'.$Item->id)); ?>">
                        التاكيد
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    <?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->where('status','not-attend')->sum('users_count')); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/not-attend-event-details/'.$Item->id)); ?>">
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
                    <?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->where('status','hold')->where('is_new_sent',0)->whereNull('is_sent')->sum('users_count')); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/hold-event-details/'.$Item->id)); ?>">
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
                    <?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->whereIn('status', ['sent'])->whereNull('is_accepted')->whereNull('is_refused')->where(function($query) { $query->where('is_new_sent',1)->orWhereNotNull('is_sent'); })->sum('users_count')); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/failed-event-details/'.$Item->id)); ?>">
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
                    <?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->where('qr_sent','yes')->sum('users_count')); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/qr-sent-event-details/'.$Item->id)); ?>">
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
                    <?php echo e($messages_count); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/congratulations-event-messages-details/'.$Item->id)); ?>">
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
                    <?php echo e(App\Models\EventUsers::where('event_id',$Item->id)->where('status','attend')->whereNull('scan')->whereNull('is_refused')->sum('users_count')); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/non-attendance-event-details/'.$Item->id)); ?>">
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
                    <?php echo e(App\Models\EventUserActions::where('event_id',$Item->id)->where('action','accept_event')->count()); ?>

                </h2>
                <span class="text-muted">
                    <a href="<?php echo e(asset('assistant_panel/confirmed-users-web-chat/'.$Item->id)); ?>">
                    	تاكيد مستخدمين الويب
                    </a>
                </span>
            </div>
        </div>
    </div>



</div>


<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/assistant/events/event_report_v1.blade.php ENDPATH**/ ?>