


<div class="row">



    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\CustomEventUsers::where('custom_event_id',$Item->id)->sum('users_count') }}
                </h2>
                <span class="text-muted">
                    <a href="javascript:void(0)">
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
                    {{ App\Models\CustomEventUsers::where('custom_event_id',$Item->id)->where('scan','yes')->sum('scan_count') }}
                </h2>
                <span class="text-muted">
                    <a href="javascript:void(0)">
                        QR
                    </a>
                </span>
            </div>
        </div>
    </div>








</div>


