@php
     $user_events = App\Models\CustomEventUsers::where('custom_event_id',$Item->id)->get();
@endphp

<div class="row">
    <div class="col-lg-12">

        {{--  <div style="margin-top:20px;display: flex;justify-content: space-around;">

        	<h3 style="font-size: 20px;border: .5px solid blue;padding: 12px 20px;border-radius: 10px;box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;">
                 عدد الدعوات : {{ $user_events->sum('users_count')  }}
            </h3>

        	<h3 style="font-size: 20px;border: .5px solid blue;padding: 12px 20px;border-radius: 10px;box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;">
                عدد الحضور : {{ $user_events->sum('scan_count')  }}
            </h3>

        </div>  --}}

        <table class="table" id="event_users_table" style="table-layout: fixed">
            <thead>
                <tr>
                    <th> م </th>
                    <th>  أسم المستخدم </th>
                    <th> رقم الهاتف </th>
                    <th> عدد الحضور </th>
                    <th> حالة الحضور </th>
                    <th> وقت الحضور </th>
                    <th>  مسح QR </th>
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
                            {{ $user_event->mobile }}
                        </td>

                      	<td style="color: #000">
                            {{ $user_event->users_count }}
                        </td>


                        <td style="color: #000">
							اكد الحضور {{ $user_event->scan_count }} / {{ $user_event->users_count }}
                        </td>

                        <td style="color: #000">
							@if(! empty($user_event->scan_at))
                          		@foreach($user_event->scan_at as $date)
                          			<span style="font-size: 14px;display: block;">
                                      {{ Carbon\Carbon::parse($date)->format('Y-m-d h:i A') }}
                          			</span>
                          		@endforeach
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



