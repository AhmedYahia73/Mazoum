@extends('admin.layouts.master')
{{-- title --}}

@section('title','أضافة موظف جديد')

@section('header')

  <style>
    .card-body .col-lg-12 , .card-body .col-lg-6 { margin-bottom: 20px }

    .card-body { padding-bottom: 0 !important}

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
            أضافة موظف جديد
          </h4>
        </div>


        {!! Form::open(['url' => "admin/assistant", 'role'=>'form','id'=>'add', 'files' => true,'method'=>'post']) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-6 {{ $errors->has('name') ? ' has-error' : '' }}">
                        <label> {{ trans('home.name') }}  <span class="text-danger">*</span> </label>
                        <input type="text" name="name" class="form-control m-input" required="required" value="{{ old('name') }}" placeholder=" {{ trans('home.name') }} ">
                        @if ($errors->has('name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('name') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label>  {{ trans('home.email') }}  <span class="text-danger">*</span>  </label>
                        <input type="email" name="email" class="form-control m-input" required="required" value="{{ old('email') }}" placeholder=" {{ trans('home.email') }}  ">
                        @if ($errors->has('email'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('email') }} </strong>
                            </span>
                        @endif
                    </div>
                  
                  	@php
                        $mobile_codes = App\Models\MobileCodes::get(['id','ar_country_name','code']);
                    @endphp

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('mobile_code') ? ' has-error' : '' }}">
                        <label>  كود الموبيل <span class="text-danger">*</span>   </label>
                        <select name="mobile_code" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" disabled selected>  أختر كود </option>
                            @if($mobile_codes != null && $mobile_codes->count() > 0)
                                @foreach($mobile_codes as $value)
                                <option value="{{ $value->code }}" @if(old('mobile_code') == $value->code) selected @endif> {{ $value->ar_country_name }} ({{ $value->code }}) </option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('mobile_code'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('mobile_code') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 {{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label>  {{ trans('home.mobile') }}  <span class="text-danger">*</span> </label>
                        <input type="text" name="mobile" class="form-control m-input" required="required" value="{{ old('mobile') }}" placeholder=" {{ trans('home.mobile') }}   ">
                        @if ($errors->has('mobile'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('mobile') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-12 {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label> {{ trans('home.password') }}  <span class="text-danger">*</span>  </label>
                    <input type="password" name="password" class="form-control m-input" required="required" value="" placeholder="  {{ trans('home.password') }}  ">
                    @if ($errors->has('password'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('password') }} </strong>
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
        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : evt.keyCode
            //return !(charCode > 31 && (charCode < 48 || charCode > 57));

            if(! (charCode >= 48 && charCode <= 57) ) {
                return false;
            }
        }
    </script>

@endsection
