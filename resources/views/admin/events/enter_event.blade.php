
    @php
        $event_family = App\Models\EventFamily::where('event_id',$Item->id)->get();
    @endphp


    @if($event_family != null && $event_family->count() > 0)

        <h2 style="margin-bottom: 20px;font-size: 20px;">  دخول المناسبة  </h2>


		 <!--begin::Form-->
        {!! Form::open(['url' => "admin/event_family_search",'role'=>'form','id'=>'event_family_search','method'=>'get']) !!}


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
              <button type="submit" form="event_family_search" class="btn btn-primary" style="margin-left:0px;margin-bottom: 30px;width:100%">
                بحث
              </button>
            </div>
            
          </div>

		{!! Form::close() !!}





        <!--begin::Form-->
        {!! Form::open(['url' => "admin/update_event_family",'role'=>'form','id'=>'update_event_family','method'=>'post']) !!}

            <input type="hidden" value="{{ $Item->id }}" name="event_id">

            @foreach ($event_family as $user)

                <div class="row" style="padding-left: 5.6%;">
                  
                  
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
                        <input type="text" name="old_event_users[{{$user->id}}][mobile]" class="form-control m-input" value="{{ $user->mobile }}"  placeholder="رقم الموبيل">
                        @if ($errors->has('mobile'))
                        <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('mobile') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="col-md-4" style="margin-top:5px">
                      
                        <span class="btn btn-danger DeletingFamily" name="{{ $user->id }}" title='Delete' style="display: inline-block;color: #FFF;cursor:pointer;margin-top: 13px;">
                            {{ trans('home.delete') }}
                        </span>

                      	@if($user->scan_qr == 'no')
                          <button type="button" class="btn btn-success open_event_family" name="{{ $user->id }}" style="margin-bottom: 5px;display: inline-block;margin-top: 17px;">
                            دخول الحفل
                          </button>
                      	@else
                          <button type="button" class="btn btn-primary" style="margin-bottom: 5px;display: inline-block;margin-top: 17px;">
                              تم الدخول مسبقا
                          </button>
                      	@endif
                    </div>

                </div>

            @endforeach


            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" form="update_event_family" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                        تحديث
                    </button>
                </div>
            </div>

        {!! Form::close() !!}

    @endif


    <h2 style="margin-bottom: 20px;font-size: 20px;"> أضافة أعضاء جديده </h2>

    <hr style="margin-bottom: 15px;border-top: 2px dashed #CCC;">

    {!! Form::open(['url' => "admin/save_event_family",'role'=>'form','id'=>'save_event_family','method'=>'post']) !!}

        <input type="hidden" value="{{ $Item->id }}" name="event_id">

        <div class="form repeater-default" style="">

            <div data-repeater-list="event_users" class="repeater-default_1">

                <div data-repeater-item class="align-items-center row repeater-default_2">
                  
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
                        <input type="text" name="mobile" class="form-control m-input" value="{{ old('mobile') }}"  placeholder="رقم الموبيل">
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
                <button type="submit" form="save_event_family" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                    حفظ
                </button>
            </div>
        </div>

    {!! Form::close() !!}


