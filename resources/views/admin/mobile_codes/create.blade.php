@extends('admin.layouts.master')
{{-- title --}}

@section('title','أضافة كود جديد')

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
          <h4 class="card-title">
            أضافة كود جديد
          </h4>
        </div>


        {!! Form::open(['url' => "admin/mobile_codes", 'role'=>'form','id'=>'add','method'=>'post', 'files' => true]) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('en_country_name') ? ' has-error' : '' }}">
                        <label> أسم الدولة باللغة الأنجليزية </label>
                        <input type="text" name="en_country_name" required class="form-control m-input" value="{{ old('en_country_name') }}"  placeholder="أسم الدولة باللغة الأنجليزية ">
                        @if ($errors->has('en_country_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('en_country_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('ar_country_name') ? ' has-error' : '' }}">
                        <label> أسم الدولة باللغة العربية </label>
                        <input type="text" name="ar_country_name" required class="form-control m-input" value="{{ old('ar_country_name') }}"  placeholder="أسم الدولة باللغة العربية ">
                        @if ($errors->has('ar_country_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('ar_country_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('country_code') ? ' has-error' : '' }}">
                        <label> كود الدولة </label>
                        <input type="text" name="country_code" required class="form-control m-input" value="{{ old('country_code') }}"  placeholder="كود الدولة ">
                        @if ($errors->has('country_code'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('country_code') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('code') ? ' has-error' : '' }}">
                        <label> كود الموبيل </label>
                        <input type="text" name="code" required class="form-control m-input" value="{{ old('code') }}"  placeholder="كود الموبيل ">
                        @if ($errors->has('code'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('code') }}</strong>
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
