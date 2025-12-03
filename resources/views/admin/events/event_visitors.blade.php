
    @php
        $event_users = App\Models\EventUsers::where('event_id',$Item->id)->get();
        $codes = App\Models\MobileCodes::get(['id','ar_country_name','code']);
    @endphp


    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ImportEventUsers">
        تحميل ملف Excel
    </button>

    @if($event_users != null && $event_users->count() > 0)


		<!--begin::Form-->
        {!! Form::open(['url' => "admin/event_users_search",'role'=>'form','id'=>'event_users_search','method'=>'get']) !!}


          <div class="row" style="border: 2px solid #777;padding: 40px 20px 20px;margin-top: 25px;margin-bottom: 35px;">


             <input type="hidden" name="event_id" value="{{ $Item->id }}">

             <div class="col-md-4 col-sm-4 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 0px">
              <label> أسم المستخدم  </label>
              <input type="text" name="name" class="form-control m-input" value="{{ old('name') }}"  placeholder="أسم المستخدم">
              @if ($errors->has('name'))
              <span class="help-block" style="color:red">
                <strong>{{ $errors->first('name') }}</strong>
              </span>
              @endif
            </div>

            <div class="col-md-4 col-sm-4 {{ $errors->has('mobile') ? ' has-error' : '' }}" style="margin-bottom: 0px">
              <label> رقم الموبيل </label>
              <input type="text" name="mobile"  class="form-control m-input" value="{{ old('mobile') }}"  placeholder="رقم الموبيل">
              @if ($errors->has('mobile'))
              <span class="help-block" style="color:red">
                <strong>{{ $errors->first('mobile') }}</strong>
              </span>
              @endif
            </div>

            <div class="col-md-4">
              <label style="visibility: hidden;"> بحث  </label>
              <button type="submit" form="event_users_search" class="btn btn-primary" style="margin-left:0px;margin-bottom: 30px;width:100%">
                بحث
              </button>
            </div>

          </div>

		{!! Form::close() !!}





        <!--begin::Form-->
        {!! Form::open(['url' => "admin/update_event_users",'role'=>'form','id'=>'update_event_users','method'=>'post']) !!}

            <input type="hidden" value="{{ $Item->id }}" name="event_id">

            @foreach ($event_users as $user)

                <div class="row" style="padding-left: 5.6%;">

                    <div class="col-md-2 col-sm-2 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                      <label> زوار الحدث </label>
                      <input type="text" name="old_event_users[{{$user->id}}][users_count]" value="{{ $user->users_count ? $user->users_count : 1 }}"  onkeypress="return isNumberKey(event)" class="form-control" style="max-width:70px;text-align:center;cursor: pointer">
                    </div>

                    <div class="col-md-4 col-sm-4 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                        <label> أسم المستخدم  </label>
                        <input type="text" name="old_event_users[{{$user->id}}][name]" required class="form-control m-input" value="{{ $user->name }}"  placeholder="أسم المستخدم">
                        @if ($errors->has('name'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="col-md-4 col-sm-4 {{ $errors->has('mobile') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                        <label> رقم الموبيل </label>
                        <input type="text" name="old_event_users[{{$user->id}}][mobile]" required class="form-control m-input" value="{{ $user->mobile }}"  placeholder="رقم الموبيل">
                        @if ($errors->has('mobile'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('mobile') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="col-md-2" style="margin-top:5px">
                        <span class="btn btn-danger DeletingUser" name="{{ $user->id }}" title='Delete' style="display: block;color: #FFF;cursor:pointer;margin-top: 17px;">
                            {{ trans('home.delete') }}
                        </span>
                    </div>

                </div>

            @endforeach


            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" form="update_event_users" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                        تحديث
                    </button>
                </div>
            </div>

        {!! Form::close() !!}

    @endif


    <h2 style="margin-bottom: 20px;font-size: 20px;"> أضافة أعضاء للحدث </h2>

    <hr style="margin-bottom: 15px;border-top: 2px dashed #CCC;">

    {!! Form::open(['url' => "admin/save_event_users",'role'=>'form','id'=>'save_event_users','method'=>'post']) !!}

        <input type="hidden" value="{{ $Item->id }}" name="event_id">

        <div class="form repeater-default" style="">

            <div data-repeater-list="event_users" class="repeater-default_1">

                <div data-repeater-item class="align-items-center row repeater-default_2">

                  	<div class="col-md-2 col-sm-2 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                      <label> زوار الحدث </label>
                      <input type="text" name="users_count" value="1"  onkeypress="return isNumberKey(event)" class="form-control" style="max-width:70px;text-align:center;cursor: pointer">
                    </div>

                    <div class="col-md-4 col-sm-4 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                        <label> أسم المستخدم  </label>
                        <input type="text" name="name" required class="form-control m-input" value="{{ old('name') }}"  placeholder="أسم المستخدم">
                        @if ($errors->has('name'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="col-md-4 col-sm-4 {{ $errors->has('mobile') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                        <label> رقم الموبيل </label>
                        <input type="text" name="mobile" required class="form-control m-input" value="{{ old('mobile') }}"  placeholder="رقم الموبيل">
                        @if ($errors->has('mobile'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('mobile') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="col-md-2" style="margin-top:0px">
                        <div data-repeater-delete="" class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill" style="display: block;height: 34px;line-height: 34px;padding: 0;">
                            <span>
                                <i class="la la-trash-o"></i>
                                <span>{{ trans('home.delete') }}</span>
                            </span>
                        </div>
                    </div>

                </div>

            </div>

            <div class="add-section">
                <div class="col-lg-12" style="padding-left: 0;margin-top: 5px;margin-bottom:20px">
                    <div data-repeater-create="" class="btn btn btn-sm btn-brand m-btn m-btn--icon m-btn--pill m-btn--wide" style="background: #82b830;color: #FFFF;padding-top: 5px;padding-left: 10px;padding-right: 10px;">
                        <span class="repeater-add">
                            <i class="la la-plus"></i>
                            <span>
                                أضافه المزيد من الأعضاء
                            </span>
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-12">
                <button type="submit" form="save_event_users" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                    حفظ
                </button>
            </div>
        </div>

    {!! Form::close() !!}




    <div class="modal fade" id="ImportEventUsers" tabindex="-1" aria-labelledby="ImportEventUsersLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ImportEventUsersLabel"> أضافه مستخدمين للدعوه </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {!! Form::open(['url' => "admin/event-user-import",'role'=>'form','id'=>'import_form','method'=>'post','files' => true]) !!}

                    <input type="hidden" name="event_id" value="{{ $Item->id }}">

                    <div class="" style="margin-top: 20px">
                        <label> الملف </label>
                        <input class="form-control" type="file" id="formFile" name="file" />
                        @if ($errors->has('image'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>

                    {!! Form::close() !!}


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                    <button type="submit" form="import_form" class="btn btn-primary"> أرسال </button>
                </div>
            </div>
        </div>
    </div>
