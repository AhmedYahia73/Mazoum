<?
@php
    $mobiles = App\Models\EventUsers::where('event_id',$Item->id)->pluck('mobile')->toArray();

    $mobiles_arr = [];

    foreach($mobiles as $phone) {
        $mobiles_arr[] = ltrim($phone,"+");
    }

    $messages_count = App\Models\;

@endphp




<div class="row"> 

    <div class="col-xl-3 col-3" style="margin-bottom:20px">
        <div class="card">
            <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                <h2>
                    {{ App\Models\ }}
                </h2>
                <span class="text-muted">
                    <a href="{{ asset('admin_panel/all-invited-users/'.$Item->id) }}">
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
 