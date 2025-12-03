@extends('admin.layouts.master')
{{-- title --}}

@section('title',trans('home.setting'))

@section('header')

  <style>

    .card-body .col-sm-12 , .card-body .col-sm-3 , .card-body .col-sm-6 , .card-body .col-sm-4 { margin-bottom: 20px }

    .card-body { padding-bottom: 0 !important}

    .card-footer {
      padding-top: 10px !important;
      padding-left: 40px !important;
      margin-top: 0 !important;
      padding-bottom: 30px !important;
    }

    .timepicker , .bootstrap-timepicker-widget {
        direction: ltr !important;
    }
  </style>

@endsection


@section('content')


<!-- Basic Inputs start -->
<section id="basic-input">

  @include('flash-message')


  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">{{ trans('home.setting') }}</h4>
        </div>


        {!! Form::open(['url' => "admin/setting",'role'=>'form','id'=>'update','method'=>'post' ,'files' => true ]) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-sm-6 {{ $errors->has('en_website_name') ? ' has-error' : '' }}">
                        <label> {{ trans('home.en_website_name') }} </label>
                        <input type="text" name="en_website_name" class="form-control m-input" required="required" value="{{ $Setting->en_website_name }}" placeholder=" {{ trans('home.en_website_name') }}  ">
                        @if ($errors->has('en_website_name'))
                        <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('en_website_name') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="col-sm-6 {{ $errors->has('ar_website_name') ? ' has-error' : '' }}">
                        <label> {{ trans('home.ar_website_name') }} </label>
                        <input type="text" name="ar_website_name" class="form-control m-input" required="required" value="{{ $Setting->ar_website_name }}" placeholder=" {{ trans('home.ar_website_name') }}  ">
                        @if ($errors->has('ar_website_name'))
                        <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('ar_website_name') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="col-sm-6 {{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label> {{ trans('home.mobile') }} </label>
                        <input type="text" name="mobile"  class="form-control m-input" required="required" value="{{ $Setting->mobile }}" placeholder=" {{ trans('home.mobile') }} ">
                        @if ($errors->has('mobile'))
                        <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('mobile') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="col-sm-6 {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label> {{ trans('home.email') }} </label>
                        <input type="email" name="email" class="form-control m-input" required="required" value="{{ $Setting->email }}" placeholder=" {{ trans('home.email') }} ">
                        @if ($errors->has('email'))
                        <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>

                  	<div class="col-sm-12 {{ $errors->has('phone_numer_id') ? ' has-error' : '' }}">
                        <label> phone numer id </label>
                        <input type="text" name="phone_numer_id"  class="form-control m-input" required="required" value="{{ $Setting->phone_numer_id }}" placeholder=" phone numer id  ">
                        @if ($errors->has('phone_numer_id'))
                        <span class="help-block" style="color:red">
                        <strong>{{ $errors->first('phone_numer_id') }}</strong>
                        </span>
                        @endif
                    </div>

                  	<div class="col-lg-12 col-sm-12 {{ $errors->has('access_token') ? ' has-error' : '' }}">
                        <label> access token  <span class="text-danger">*</span>  </label>
                        <textarea name="access_token" class=" form-control" rows="10" placeholder=" access token  ">{{ $Setting->access_token }}</textarea>
                        @if ($errors->has('en_description'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('access_token') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-sm-12 {{ $errors->has('logo') ? ' has-error' : '' }}">
                        <label> اللوجو </label>
                        <input class="form-control" type="file" id="formFile" name="logo" accept="image/*" />
                        @if ($errors->has('logo'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('logo') }}</strong>
                            </span>
                        @endif
                        <img id="imgPreview" src="{{ $Setting->logo }}?{{ rand() }}" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('en_description') ? ' has-error' : '' }}">
                        <label> {{ trans('home.en_description') }}  <span class="text-danger">*</span>  </label>
                        <textarea name="en_description" id="editor2" class=" form-control" rows="10" placeholder=" {{ trans('home.en_description') }}  ">{{ $Setting->en_description }}</textarea>
                        @if ($errors->has('en_description'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('en_description') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('ar_description') ? ' has-error' : '' }}">
                        <label> {{ trans('home.ar_description') }}  <span class="text-danger">*</span>  </label>
                        <textarea name="ar_description" id="editor2" class=" form-control" rows="10" placeholder=" {{ trans('home.ar_description') }}  ">{{ $Setting->ar_description }}</textarea>
                        @if ($errors->has('ar_description'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('ar_description') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('en_key_words') ? ' has-error' : '' }}">
                        <label> {{ trans('home.en_key_words') }}  <span class="text-danger">*</span>  </label>
                        <textarea name="en_key_words" required class=" form-control" rows="10" placeholder=" {{ trans('home.en_key_words') }}  ">{{ $Setting->en_key_words }}</textarea>
                        @if ($errors->has('en_key_words'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('en_key_words') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('ar_key_words') ? ' has-error' : '' }}">
                        <label> {{ trans('home.ar_key_words') }}  <span class="text-danger">*</span>  </label>
                        <textarea name="ar_key_words" required class=" form-control" rows="10" placeholder=" {{ trans('home.ar_key_words') }} ">{{ $Setting->ar_key_words }}</textarea>
                        @if ($errors->has('ar_key_words'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('ar_key_words') }} </strong>
                            </span>
                        @endif
                    </div>

                </div>

            </div>

            <div class="card-footer" style="padding-left: 25px !important;margin-top: 0px !important;">
              <button type="submit" form="update" class="btn btn-primary mr-2">
                {{ trans('home.update') }}
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
   <script>
      // is number //
      function isNumberKey(evt){
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
         return true;
      }
   </script>
@endsection








