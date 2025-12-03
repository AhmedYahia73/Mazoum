
    @php
        $x = 1;
        $event_users = App\Models\EventUsers::where('event_id',$Item->id)->get();
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
          <button type="button" form="send_event_users" class="old_send_btn btn btn-success">
            <span> أرسال ميتا </span>
            <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
          </button>
        </b>
        <b>
            <button type="button" class="btn btn-success mr-2"  data-bs-toggle="modal" data-bs-target="#NewSendCustomMessage">
                 <span> أرسال</span>
                <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
            </button>
        </b>
        <b>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessage">
                أرسال رسالة خاصة
            </button>
        </b>
      	<b>
            <button class="btn btn-success" onclick="return SendCongratulations({{$Item->id}});">
                أرسال تهنئة
            </button>
        </b>
      	<button type="button" id="delete_selected_items_btn" class="btn btn-danger mr-2">
            حذف العناصر المختارة
        </button>

    </div>


    {!! Form::open(['url' => "admin/send_event_users",'role'=>'form','id'=>'send_event_users','method'=>'post','files' => true]) !!}


        <input type="hidden" name="event_id" value="{{ $Item->id }}">


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
                            <th scope="col"  style="text-align: center;font-weight: normal;">الحالة</th>

                            <th scope="col"  style="text-align: center;font-weight: normal;">from</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">sent</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">delivered</th>
                          	<th scope="col"  style="text-align: center;font-weight: normal;">read</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">accepted</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">qr</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">refused</th>
                            <th scope="col"  style="text-align: center;font-weight: normal;">New Send</th>

                            <th scope="col"  style="text-align: center;font-weight: normal;"> # </th>

                            <th scope="col"  style="text-align: center;font-weight: normal;"> Qr Image </th>

                            <th scope="col">#</th>
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

                                    <td style="direction: ltr;text-align: center;" class="{{  App\Models\EventUsers::where('event_id',$Item->id)->where('mobile',$user->mobile)->count() > 1 ? 'repeated_number' : ''  }}">
                                        {{ $user->mobile }}
                                    </td>

                                    <td style="text-align: center;">
                                        {{ $user->status }}
                                    </td>

                                  	<td style="text-align: center;">
                                        {{ $user->sent_from }}
                                    </td>

                                  	<td style="text-align: center;">
                                        @if($user->is_sent == 'yes')
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	@else
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        @endif
                                    </td>

                                  	<td style="text-align: center;">
                                        @if($user->is_delivered == 'yes')
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	@else
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        @endif
                                    </td>

                                  	<td style="text-align: center;">
                                        @if($user->is_read == 'yes')
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	@else
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        @endif
                                    </td>

                                  	<td style="text-align: center;">
                                        @if($user->is_accepted == 'yes')
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	@else
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        @endif
                                    </td>

                                  	<td style="text-align: center;">
                                        @if($user->qr_sent == 'yes')
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	@else
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        @endif
                                    </td>

                                  	<td style="text-align: center;">
                                        @if($user->is_refused == 'yes')
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	@else
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        @endif
                                    </td>

                                  	<td style="text-align: center;">
                                        @if($user->is_new_sent == 1)
                                      		<i class="fa fa-check me-1" style="color:green"></i>
                                      	@else
                                      		<i class="fa fa-times me-1" style="color:red"></i>
                                        @endif
                                    </td>

                                    <td>
                                        <span style="display:inline-block;font-weight: bold;color: #000;cursor: pointer;" data-bs-toggle="modal" data-bs-target="#editMobileModal{{ $user->id }}">
                                            <i class="bx bx-edit-alt me-1"></i>
                                        </span>
                                    </td>

                                  	<td style="text-align: center;">
										@php
                                      		$qr_image = App\Models\Qr_Code::where('event_user_id',$user->id)->latest()->first();
                                      	@endphp

                                      	@if($qr_image)
                                      		<a href="{{ asset('qr_code/'.$qr_image->qr) }}" target="_blank">
                                              <img src="{{ asset('qr_code/'.$qr_image->qr) }}?{{ rand() }}" style="width:150px;height:150px">
                                      		</a>
                                      	@endif
                                    </td>

                                    <td>
                                      <div>
                                          <a href="{{ asset('admin/event-user-history/'.$user->id) }}" class="btn btn-primary" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-eye" aria-hidden="true"></i>
                                          </a>

                                          @if($user->scan_count < $user->users_count)
                                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendLoginUser{{ $user->id }}" name="{{ $user->id }}" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;background: green;border-color: green;">
                                              <i class="fa fa-sign-in" aria-hidden="true"></i>
                                          </button>
                                          @endif

                                          <button type="button" class="btn btn-info send_qr_code" name="{{ $user->id }}" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-qrcode" aria-hidden="true"></i>
                                          </button>

                                          <button type="button" class="btn btn-info send_new_qr_code" name="{{ $user->id }}" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;background-color: brown;border-color: brown;">
                                              <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                          </button>

                                          <button type="button" class="btn btn-primary is_send_event" name="{{ $user->id }}" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-bars-progress" aria-hidden="true"></i>
                                          </button>
                                          <button type="button" class="btn btn-success accept_event" name="{{ $user->id }}" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-check" aria-hidden="true"></i>
                                          </button>
                                          <button type="button" class="btn btn-danger refuse_event" name="{{ $user->id }}" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-times" aria-hidden="true"></i>
                                          </button>
                                          <button type="button" class="btn btn-info qr_is_send" name="{{ $user->id }}" style="width: 37px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;">
                                              <i class="fa fa-qrcode" aria-hidden="true"></i>
                                          </button>

                                          <button type="button" class="btn btn-primary send_location" name="{{ $user->id }}" style="width: 80px;height: 33px;margin-bottom: 5px;padding: 0;line-height: 33px;background: green;border-color: green;">
                                              <i class="fa fa-location-arrow" aria-hidden="true"></i>
                                          </button>

                                          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#SendCustomCongratulationMessage{{ $user->id }}" style="margin-bottom:5px">
                                             تهنئه
                                          </button>
                                       	  <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#SendCustomApologizeMessage{{ $user->id }}">
                                             اعتذار
                                          </button>
                                      </div>
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
                <button type="button" form="send_event_users" class="old_send_btn btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                    <span> أرسال ميتا </span>
                    <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
                </button>
                <b>
                    <button type="button" class="btn btn-success mr-2" style="margin-left:0px;margin-bottom: 30px;"  data-bs-toggle="modal" data-bs-target="#NewSendCustomMessage">
                      <span> أرسال</span>
                      <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
                    </button>
                </b>
                <button type="button" class="delete_selected_items_btn btn btn-danger mr-2"  style="margin-left:0px;margin-bottom: 30px;">
                    حذف العناصر المختارة
                </button>
            </div>
        </div>


        <div class="modal fade" id="SendCustomMessage" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="SendCustomMessageLabel">  رسالة خاصة </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div style="margin-bottom: 25px;display: flex;justify-content: start;align-items: center;">
                        <input type="radio" id="old_send" name="sending_type" value="old_send" checked style="width: 17px;height: 17px;">
                        <label for="old_send" style="cursor: pointer;margin-right: 5px;">  أرسال ميتا   </label>

                        <input type="radio" id="new_send" name="sending_type" value="new_send" style="width: 17px;height: 17px;margin-right: 30px;">
                        <label for="new_send" style="cursor: pointer;margin-right: 5px;">أرسال </label>
                    </div>

                    <div class="">
                        <label> محتوي الرسالة </label>
                        <textarea name="message" rows="5" class="form-control" placeholder="محتوي الرسالة">{{ old('message') }}</textarea>
                    </div>

                    <div class="" style="margin-top: 20px">
                        <label> صوره  <span class="text-danger">*</span> </label>
                        <input class="form-control" type="file" id="formFileImage" name="file" />
                        <img id="imgPreview" src="{{ $Item->file }}?{{rand()}}" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
                        @if ($errors->has('image'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                  <button type="button" onclick="sendMessageToSelected()" class="btn btn-primary"> أرسال </button>
                </div>
              </div>
            </div>
        </div>


        <div class="modal fade" id="NewSendCustomMessage" tabindex="-1" aria-labelledby="NewSendCustomMessageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="NewSendCustomMessageLabel"> أرسال </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div style="margin-bottom: 25px;display: flex;justify-content: start;align-items: center;">
                        <input type="radio" id="image_item" name="file_type" value="image" checked style="width: 17px;height: 17px;">
                        <label for="image_item" style="cursor: pointer;margin-right: 5px;"> صوره </label>

                        <input type="radio" id="video_item" name="file_type" value="video" style="width: 17px;height: 17px;margin-right: 30px;">
                        <label for="video_item" style="cursor: pointer;margin-right: 5px;"> فيديو </label>
                    </div>


                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                  <button type="button" onclick="NewSendMessageToSelected()" class="btn btn-primary"> أرسال </button>
                </div>
              </div>
            </div>
        </div>

    {!! Form::close() !!}




    @if($event_users != null && $event_users->count() > 0)

        @foreach ($event_users as $user)


            {!! Form::open(['url' => "admin/send-congratulation-message",'role'=>'form','id'=>'send-congratulation-message'.$user->id,'method'=>'post','files' => true]) !!}

                <input type="hidden" name="event_id" value="{{ $Item->id }}">

                <div class="modal fade" id="SendCustomCongratulationMessage{{ $user->id }}" tabindex="-1" aria-labelledby="SendCongratulationMessageLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendCongratulationMessageLabel{{ $user->id }}">
                            ارسال تهنئه للعضو
                            (  {{ $user->name }} )
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div class="modal-body">
                            <div class="">
                                <label> محتوي الرسالة </label>
                                <textarea name="msg1" id="msg_v1_{{ $user->id }}" rows="5" class="form-control msg1" placeholder="محتوي الرسالة">{{ old('msg1') }}</textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="submit" form="send-congratulation-message{{ $user->id }}" class="btn btn-primary" data-id="{{ $user->id }}"> أرسال </button>
                        </div>
                        </div>
                    </div>
                </div>

            {!! Form::close() !!}


            {!! Form::open(['url' => "admin/send-apologize-message",'role'=>'form','id'=>'send-apologize-message'.$user->id,'method'=>'post','files' => true]) !!}

                <input type="hidden" name="event_id" value="{{ $Item->id }}">

                <div class="modal fade" id="SendCustomApologizeMessage{{ $user->id }}" tabindex="-1" aria-labelledby="SendApologizeMessageLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendApologizeMessageLabel{{ $user->id }}">
                            ارسال اعتذار للعضو
                            (  {{ $user->name }} )
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div class="modal-body">
                            <div class="">
                                <label> محتوي الرسالة </label>
                                <textarea name="msg2" id="msg_v2_{{ $user->id }}" rows="5" class="form-control msg2" placeholder="محتوي الرسالة">{{ old('msg2') }}</textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="submit" form="send-apologize-message{{ $user->id }}"  class="btn btn-primary" data-id="{{ $user->id }}"> أرسال </button>
                        </div>
                        </div>
                    </div>
                </div>

            {!! Form::close() !!}


            {!! Form::open(['url' => "admin/login-user/".$user->id,'role'=>'form','id'=>'send-login-user'.$user->id,'method'=>'get','files' => true]) !!}

                <input type="hidden" name="event_id" value="{{ $Item->id }}">

                <div class="modal fade" id="SendLoginUser{{ $user->id }}" tabindex="-1" aria-labelledby="SendLoginUserLabel{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendLoginUserLabel{{ $user->id }}">
                            هل تريد حقا تاكيد دخول العضو
                            (  {{ $user->name }} )
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div class="modal-body">
                            <div class="">
                                <label> عدد الدعوات	  </label>
                                <select name="users_count" class="form-control m-bootstrap-select m_selectpicker" required>
                                    <option value="" disabled selected> اختر عدد الدعوات </option>
                                    @for ($count=1;$count <= ($user->users_count - $user->scan_count); $count++)
                                        <option value="{{ $count }}"> {{ $count }} </option>
                                    @endfor
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="submit" form="send-login-user{{ $user->id }}" class="btn btn-primary" data-id="{{ $user->id }}"> دخول </button>
                        </div>
                        </div>
                    </div>
                </div>

            {!! Form::close() !!}


            <div class="modal fade" id="editMobileModal{{ $user->id }}" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendCustomMessageLabel"> تعديل رقم الموبيل </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            {!! Form::open(['url' => "admin/update-user-mobile", 'role'=>'form','id'=>'editFormMobileModal'.$user->id,'method'=>'post']) !!}

                            <input type="hidden" name="event_user_id" value="{{ $user->id }}">

                            <div class="" style="margin-bottom: 20px">
                                <label> عدد الدعوات	 </label>
                                <input type="text" class="form-control" name="users_count" value="{{ $user->users_count }}" placeholder="عدد الدعوات  " required>
                            </div>

                            <div class="" style="margin-bottom: 20px">
                                <label>   أسم المستخدم	 </label>
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}" placeholder="أسم المستخدم	  " required>
                            </div>

                            <div class="">
                                <label> رقم الموبيل </label>
                                <input type="text" class="form-control" name="mobile" value="{{ $user->mobile }}" placeholder="رقم الموبيل" required>
                            </div>

                            {!! Form::close() !!}

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                            <button type="submit" form="editFormMobileModal{{ $user->id }}" class="btn btn-primary"> تحديث </button>
                        </div>
                    </div>
                </div>
            </div>


        @endforeach

    @endif

