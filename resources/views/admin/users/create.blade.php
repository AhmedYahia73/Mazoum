@extends('admin.layouts.master')
{{-- title --}}

@section('title',trans('home.add_user'))

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
            {{ trans('home.add_user') }}
          </h4>
        </div>


        {!! Form::open(['url' => "admin/users", 'role'=>'form','id'=>'add','method'=>'post']) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-12 col-sm-12 {{ $errors->has('name') ? ' has-error' : '' }}">
                        <label>  {{ trans('home.name') }}  <span class="text-danger">*</span>  </label>
                        <input type="text" name="name" class="form-control m-input" required="required" value="{{ old('name') }}" placeholder="  {{ trans('home.name') }}   ">
                        @if ($errors->has('name'))
                             <span class="help-block" style="color:red">
                                  <strong>{{ $errors->first('name') }} </strong>
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



                    <div class="col-lg-6 col-sm-6 {{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label>  {{ trans('home.mobile') }} <span class="text-danger">*</span>   </label>
                        <input type="text" name="mobile" class="form-control m-input" required="required" value="{{ old('mobile') }}" placeholder=" {{ trans('home.mobile') }} ">
                        @if ($errors->has('mobile'))
                             <span class="help-block" style="color:red">
                                  <strong>{{ $errors->first('mobile') }} </strong>
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


