@extends('admin.layouts.master')
{{-- title --}}

@section('title','أضافة حدث جديد')

@section('header')

  <style>

    .card-body .col-sm-12 , .card-body .col-sm-4 , .card-body .col-sm-6 { margin-bottom: 20px }

    .card-body { padding-bottom: 0 !important}

    .card-footer {
      padding-top: 10px !important;
      padding-left: 40px !important;
      margin-top: 0 !important;
      padding-bottom: 30px !important;
    }

    .un_active {
      display: none
    }

    .active {
      display: block
    }

    .hasTime .flatpickr-innerContainer {
        display: none !important
    }

    .hasTime .flatpickr-months {
        display: none !important
    }

    .light-style .flatpickr-calendar.hasTime .flatpickr-time {
        direction: ltr;
    }

  </style>

  <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/flatpickr/flatpickr.css" />
  <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/pickr/pickr-themes.css" />

@endsection

@section('content')



<!-- Basic Inputs start -->
<section id="basic-input">

  @include('flash-message')


  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            أضافة حدث جديد
          </h4>
        </div>


        {!! Form::open(['url' => "admin/events", 'role'=>'form','id'=>'add','method'=>'post', 'files' => true]) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 col-sm-6 {{ $errors->has('assistant_id') ? ' has-error' : '' }}">
                        <label> الموظفين  <span class="text-danger">*</span> </label>
                        <select name="assistant_id" id="assistant_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true">  الموظفين  </option>
                            @foreach (Assistants() as $key => $value)
                                <option value="{{ $key }}" @if(old('assistant_id') == $key) {{ 'selected' }} @endif> {{ $value }} </option>
                            @endforeach
                        </select>
                        @if ($errors->has('assistant_id'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('assistant_id') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-md-6 col-sm-6 {{ $errors->has('country_code') ? ' has-error' : '' }}">
                        <label> الدوله  <span class="text-danger">*</span> </label>
                        <select name="country_code" id="country_code" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true">  الدوله  </option>
                            <option value="kw" @if(old('country_code') == 'kw') {{ 'selected' }} @endif> الكويت </option>
                            <option value="sa" @if(old('country_code') == 'sa') {{ 'selected' }} @endif> السعودية </option>
                        </select>
                        @if ($errors->has('country_code'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('country_code') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('title') ? ' has-error' : '' }}">
                        <label> عنوان الحدث </label>
                        <input type="text" name="title" required class="form-control m-input" value="{{ old('title') }}"  placeholder="عنوان الحدث ">
                        @if ($errors->has('title'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-md-6 col-sm-6 {{ $errors->has('user_id') ? ' has-error' : '' }}">
                        <label> المستخدمين  <span class="text-danger">*</span> </label>
                        <select name="user_id" id="user_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true">  المستخدمين  </option>
                            @foreach (Users() as $key => $value)
                                <option value="{{ $key }}" @if(old('user_id') == $key) {{ 'selected' }} @endif> {{ $value }} </option>
                            @endforeach
                        </select>
                        @if ($errors->has('user_id'))
                         <span class="help-block" style="color:red">
                             <strong>{{ $errors->first('user_id') }}</strong>
                         </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('address') ? ' has-error' : '' }}">
                        <label> موقع الحدث </label>
                        <input type="text" name="address" required class="form-control m-input" value="{{ old('address') }}"  placeholder="موقع الحدث ">
                        @if ($errors->has('address'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-sm-6 {{ $errors->has('showing_qr') ? ' has-error' : '' }}">
                        <label>  هل تريد أظهار ال QR <span class="text-danger">*</span> </label>
                        <select name="showing_qr" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="yes" @if(old('showing_qr') == 'yes') {{ 'selected' }} @endif> نعم </option>
                            <option value="no"  @if(old('showing_qr') == 'no')  {{ 'selected' }} @endif> لا </option>
                        </select>
                        @if ($errors->has('showing_qr'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('showing_qr') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('have_reminder') ? ' has-error' : '' }}">
                        <label>  هل تريد تفعيل رسائل التذكير <span class="text-danger">*</span> </label>
                        <select name="have_reminder" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="yes" @if(old('have_reminder') == 'yes') {{ 'selected' }} @endif> نعم </option>
                            <option value="no"  @if(old('have_reminder') == 'no')  {{ 'selected' }} @endif> لا </option>
                        </select>
                        @if ($errors->has('have_reminder'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('have_reminder') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('can_replay_messages') ? ' has-error' : '' }}">
                        <label>  هل تريد تفعيل الرد علي الرسائل <span class="text-danger">*</span> </label>
                        <select name="can_replay_messages" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="yes" @if(old('can_replay_messages') == 'yes') {{ 'selected' }} @endif> نعم </option>
                            <option value="no"  @if(old('can_replay_messages') == 'no')  {{ 'selected' }} @endif> لا </option>
                        </select>
                        @if ($errors->has('can_replay_messages'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('can_replay_messages') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('enable_resend_again') ? ' has-error' : '' }}">
                        <label>  هل تريد تفعيل اعاده ارسال <span class="text-danger">*</span> </label>
                        <select name="enable_resend_again" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="yes" @if(old('enable_resend_again') == 'yes') {{ 'selected' }} @endif> نعم </option>
                            <option value="no"  @if(old('enable_resend_again') == 'no')  {{ 'selected' }} @endif> لا </option>
                        </select>
                        @if ($errors->has('enable_resend_again'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('enable_resend_again') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('sending_type') ? ' has-error' : '' }}">
                        <label>  نوع الأرسال <span class="text-danger">*</span> </label>
                        <select name="sending_type" class="form-control m-bootstrap-select m_selectpicker" required>
                            <option value="" disabled selected="true"> نعم / لا </option>
                            <option value="old_send"      @if(old('sending_type') == 'old_send') {{ 'selected' }} @endif>  أرسال ميتا  </option>
                            <option value="new_send"      @if(old('sending_type') == 'new_send')  {{ 'selected' }} @endif>أرسال </option>
                            <option value="not_available" @if(old('sending_type') == 'not_available')  {{ 'selected' }} @endif> غير متاح </option>
                        </select>
                        @if ($errors->has('sending_type'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('sending_type') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('lat') ? ' has-error' : '' }}">
                        <label> دوائر العرض </label>
                        <input type="text" name="lat" class="form-control m-input" value="0"  placeholder="دوائر العرض ">
                        @if ($errors->has('lat'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('lat') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('long') ? ' has-error' : '' }}">
                        <label> خطوط الطول </label>
                        <input type="text" name="long" class="form-control m-input" value="0"  placeholder="خطوط الطول ">
                        @if ($errors->has('long'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('long') }}</strong>
                            </span>
                        @endif
                    </div>



                    <div class="col-sm-6 {{ $errors->has('date') ? ' has-error' : '' }}">
                        <label> تاريخ الحدث <span class="text-danger">*</span> </label>
                        <input type="text" name="date" required class="form-control m-input" value="{{ old('date') }}"  placeholder="YYYY-MM-DD" id="flatpickr-date">
                        @if ($errors->has('date'))
                             <span class="help-block" style="color:red">
                                  <strong>{{ $errors->first('date') }} </strong>
                             </span>
                        @endif
                    </div>

                    <div class="col-sm-6 {{ $errors->has('time') ? ' has-error' : '' }}">
                        <label> وقت الحدث <span class="text-danger">*</span> </label>
                        <input type="text" name="time" id="flatpickr-date3" required class="form-control m-input" value="{{ old('time') }}"  placeholder="وقت الحدث">
                        @if ($errors->has('date'))
                             <span class="help-block" style="color:red">
                                  <strong>{{ $errors->first('time') }} </strong>
                             </span>
                        @endif
                    </div>

                    <div class="col-lg-12 col-sm-12 {{ $errors->has('color') ? ' has-error' : '' }}">
                        <label> اللون  </label>
                        <input type="color" name="color" required class="form-control m-input" value="{{ old('color') }}"  placeholder=" اللون ">
                        @if ($errors->has('color'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('color') }}</strong>
                            </span>
                        @endif
                    </div>


                    <div class="col-sm-6 {{ $errors->has('file') ? ' has-error' : '' }}">
                        <label> تصميم الدعوه <span class="text-danger">*</span> </label>
                        <input class="form-control" type="file" id="formFile" required name="file" />
                        @if ($errors->has('file'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('file') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-sm-6 {{ $errors->has('image') ? ' has-error' : '' }}">
                        <label> تصميم QR   </label>
                        <input class="form-control" type="file" id="formFile" name="image" />
                        @if ($errors->has('image'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-sm-12 {{ $errors->has('video') ? ' has-error' : '' }}">
                        <label> video  </label>
                        <input class="form-control" type="file" id="formVideo" name="video" accept="video/*" />
                        @if ($errors->has('video'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('video') }}</strong>
                            </span>
                        @endif
                    </div>

                </div>


            </div>

            <div class="card-footer" style="padding-left: 25px !important;margin-top: 0px !important;">
              <button type="submit" form="add" class="btn btn-primary mr-2">
                {{ trans('home.save') }}
              </button>
            </div>

        {!! Form::close() !!}
         <!--end::Form-->


      </div>
    </div>
  </div>
</section>
<!-- Basic Inputs end -->



@endsection



@section('footer')


    <!-- Vendors JS -->
    <script src="{{asset('admin/assets')}}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/pickr/pickr.js"></script>

    <!-- Page JS -->
    <script>
        'use strict';

        (function () {

            // Flat Picker
            // --------------------------------------------------------------------
            const  flatpickrDateTime = document.querySelector('#flatpickr-date');

            // Datetime
            if (flatpickrDateTime) {
                flatpickrDateTime.flatpickr({
                    enableTime: false,
                    dateFormat: 'Y-m-d'
                });
            }

        })();

    </script>


@endsection










