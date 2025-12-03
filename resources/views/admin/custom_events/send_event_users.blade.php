
    @php
        $x = 1;
        $event_users = App\Models\CustomEventUsers::where('custom_event_id',$Item->id)->get();
    @endphp


    <div style="margin-bottom: 20px">
        <b>
            <label class="btn btn-warning">
                <input type="checkbox" for="checkbox_select" id="select_all" name="select_all">
                <span style="display: inline-block;padding-right:10px">
                  <span> أختر الكل </span>
                </span>
            </label>
        </b>

        <b>
            <button type="button" class="new_send_btn btn btn-success mr-2">
                أرسال حديث
            </button>
        </b>

      	<button type="button" id="delete_selected_items_btn" class="btn btn-danger mr-2">
            حذف العناصر المختارة
        </button>

    </div>


    {!! Form::open(['url' => "admin/send_event_users",'role'=>'form','id'=>'send_event_users','method'=>'post','files' => true]) !!}


        <input type="hidden" name="custom_event_id" value="{{ $Item->id }}">


        <div class="row">
            <div class="col-lg-12">

              <div class="">

                <table class="table" id="send_events_table">
                    <thead>
                        <tr>
                            <th scope="col"  style="text-align: center;font-weight: normal;">#</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">عدد الدعوات</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;"> أسم المستخدم </th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">رقم الهاتف </th>

                            <th scope="col"  style="text-align: center;font-weight: normal;"> Qr Image </th>

                        </tr>
                    </thead>
                    <tbody>
                        @if($event_users != null && $event_users->count() > 0)

                            @foreach ($event_users as $user)

                                <tr>
                                    <th scope="row"  style="text-align: center;">
                                      <div style="display: flex;align-items: center;justify-content: center;">
                                         <span style="font-size: 20px;"> {{ $x }} </span>
                                         <input type="checkbox" class="check_items" name="users[{{$x}}][id]" value="{{ $user->id }}" id="user{{ $user->id }}" style="cursor: pointer;margin-right: 5px;width: 20px;height: 20px;">
                                      </div>
                                    </th>
                                    <td style="text-align: center;">
                                        <input type="text" name="users[{{$x}}][users_count]" value="{{ $user->users_count ? $user->users_count : 1 }}"  onkeypress="return isNumberKey(event)" class="form-control" style="max-width:70px;text-align:center;cursor: pointer">
                                    </td>
                                    <td style="text-align: center;">
                                        <label for="user{{ $user->id }}" style="cursor: pointer">
                                            {{ $user->name }}
                                        </label>
                                    </td>
                                    <td style="direction: ltr;text-align: center;">
                                        {{ $user->mobile }}
                                    </td>


                                  	<td class="qr-cell">
                                        <a href="{{ $user->qr }}" download="{{ $user->name }}">
                                            <img src="{{ $user->qr }}" style="max-width:150px">
                                        </a>
                                    </td>


                                </tr>





                                @php $x = $x + 1; @endphp

                            @endforeach

                        @else
                        <tr>
                            <td colspan="6">
                                لا يوجد مستخدمين
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>

              </div>

            </div>
        </div>


        <div class="row"  style="margin-top: 20px">
            <div class="col-lg-12">

                <b>
                    <button type="button" class="new_send_btn btn btn-success mr-2" style="margin-left:0px;margin-bottom: 30px;">
                        أرسال حديث
                    </button>
                </b>

                <button type="button" class="delete_selected_items_btn btn btn-danger mr-2"  style="margin-left:0px;margin-bottom: 30px;">
                    حذف العناصر المختارة
                </button>
            </div>
        </div>


    {!! Form::close() !!}



