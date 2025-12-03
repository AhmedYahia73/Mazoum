@php
     $non_attendance = App\Models\EventUsers::where('event_id',$Item->id)->where('status','attend')->where('is_accepted','yes')->whereNull('scan')->whereNull('is_refused')->get();
@endphp



	<div style="margin-bottom: 20px">
        <b>
            <label class="btn btn-warning">
                <input type="checkbox" for="checkbox_select" id="select_all_v2" name="select_all">
                <span style="display: inline-block;padding-right:10px">
                    أختر الكل
                </span>
            </label>
        </b>

      	<b>
        	<button type="submit" form="send_event_users_v2" class="btn btn-success">
              <span> أرسال </span>
              <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
           </button>
        </b>

      	<button type="button" id="delete_selected_items_btn_v2" class="btn btn-danger mr-2">
            حذف العناصر المختارة
        </button>

    </div>




    {!! Form::open(['url' => "admin/send_event_users",'role'=>'form','id'=>'send_event_users_v2','method'=>'post','files' => true]) !!}


        <input type="hidden" name="event_id" value="{{ $Item->id }}">



<div class="row">
    <div class="col-lg-12">

        <table class="table" id="non_attendance_table">
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

                @foreach ($non_attendance as $user_event)

                    <tr>
                        <th scope="row"  style="text-align: center;">
                                                      <span style="font-size: 20px;"> {{ $x }} </span>
                            <input type="checkbox" class="check_items" name="users[{{$x}}][id]" value="{{ $user_event->id }}" id="user{{ $user_event->id }}" style="cursor: pointer;margin-right: 5px;width: 20px;height: 20px;">
                        </th>

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
                          {{ $user_event->scan_at != null ? Carbon\Carbon::parse($user_event->scan_at)->format('Y-m-d h:i A') : null }}
                        </td>
                        <td style="color: #000">
                            {{ $user_event->scan == 'yes' ? 'نعم' : 'لا' }}
                        </td>
                    </tr>

                    @php $x = $x + 1; @endphp

                @endforeach

            </tbody>
        </table>
    </div>
</div>


	<div class="row"  style="margin-top: 20px">
            <div class="col-lg-12">
                <button type="submit" form="send_event_users_v2" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                  <span> أرسال </span>
              	   <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
                </button>
                <button type="button" style="margin-left:0px;margin-bottom: 30px;" class="delete_selected_items_btn_v2 btn btn-danger mr-2">
                      حذف العناصر المختارة
                 </button>
            </div>
        </div>


    {!! Form::close() !!}


