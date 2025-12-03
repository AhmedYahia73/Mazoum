@php
     $user_events = App\Models\EventUsers::where('event_id',$Item->id)->where('scan','!=',null)->get();
@endphp

<div class="row">
    <div class="col-lg-12">

        <table class="table" id="event_users_table">
            <thead>
                <tr>
                    <th scope="col"> م </th>
                    <th scope="col">  أسم المستخدم </th>
                    <th scope="col"> عدد الحضور </th>
                    <th scope="col"> رقم الهاتف </th>
                    <th scope="col"> حالة الحضور </th>
                    <th scope="col"> وقت الحضور </th>
                    <th scope="col">  مسح QR </th>
                </tr>
            </thead>
            <tbody>
                @php $x = 1; @endphp

                @foreach ($user_events as $user_event)

                    <tr>
                        <td style="color: #000">
                            {{ $x }}
                        </td>
                        <td style="color: #000">
                            {{ $user_event->name }}
                        </td>
                      	<td style="color: #000">
                            {{ $user_event->users_count }}
                        </td>
                        <td style="color: #000">
                            {{ $user_event->mobile }}
                        </td>
                        <td style="color: #000">
                            @if($user_event->status == 'attend')
                                اكد الحضور
                            @endif
                            @if($user_event->status == 'not-attend')
                                اعتذر عن الحضور
                            @endif
                            @if($user_event->status == 'hold')
                                لم يرسل دعوه بعد
                            @endif
                        </td>

                        <td style="color: #000">
							@if(! empty($user_event->scan_at) && is_array($user_event->scan_at))
                          		@foreach($user_event->scan_at as $date)
                          			<span style="font-size: 14px;display: block;">
                                      {{ Carbon\Carbon::parse($date)->format('Y-m-d h:i:s A') }}
                          			</span>
                          		@endforeach
                          	@else
                             {{ $user_event->scan_at != null ? Carbon\Carbon::parse($user_event->scan_at)->format('Y-m-d h:i:s A') : '' }}
                          	@endif
                        </td>

                        <td style="color: #000">
							{{ $user_event->scan_count }} / {{ $user_event->users_count }}
                        </td>
                    </tr>

                    @php $x = $x + 1; @endphp

                @endforeach

            </tbody>
        </table>
    </div>
</div>



