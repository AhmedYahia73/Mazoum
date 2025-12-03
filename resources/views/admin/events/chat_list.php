

@php
    $event_user_ids = App\Models\EventUserActions::where('event_id',$Item->id)->pluck('event_user_id')->toArray();
    $event_user_ids = array_unique($event_user_ids);
    $event_users = App\Models\EventUsers::whereIn('id',$event_user_ids)->get();
@endphp



<div class="row">

    @if($event_users != null && $event_users->count() > 0)

        @foreach ($event_users as $event_user)
            <div class="col-md-4">
                <div style="margin: auto;text-align: center;">
                    <a href="{{ asset('admin/event-chat/'.$event_user->id) }}" target="_blank">
                        <img src="{{ asset('user-profile.png') }}" style="width: 115px;margin-bottom: 10px;">
                    </a>
                    <div style="text-align: center">
                        <a href="{{ asset('admin/event-chat/'.$event_user->id) }}" target="_blank">
                            <h5 class="mt-0"> {{ $event_user->name }} </h5>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

    @else
        <div class="col-md-12">
            لا يوجد محادثات
        </div>
    @endif

</div>
