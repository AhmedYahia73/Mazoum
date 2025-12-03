@extends('admin.layouts.master')
{{-- title --}}

@section('title','أضافة باقة معزوم')


@section('header')

  <style>

    .card-body .col-sm-12 , .card-body .col-sm-4 , .card-body .col-sm-6 , .card-body .col-md-6 { margin-bottom: 20px }

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
            أضافة باقة معزوم
          </h4>
        </div>


        {!! Form::open(['url' => "admin/reservation", 'role'=>'form','id'=>'add','method'=>'post', 'files' => true]) !!}

            <div class="card-body">

                <div class="row">

                  
                  	<div class="col-lg-6 col-md-6 {{ $errors->has('employee_name') ? ' has-error' : '' }}">
                        <label> اسم الموظف </label>
                        <input type="text" name="employee_name" required class="form-control m-input" value="{{ old('employee_name') }}"  placeholder="اسم الموظف ">
                        @if ($errors->has('employee_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('employee_name') }}</strong>
                            </span>
                        @endif
                    </div>
                  
                  	<div class="col-lg-6 col-md-6 {{ $errors->has('office_name') ? ' has-error' : '' }}">
                        <label> اسم المكتب </label>
                        <input type="text" name="office_name" required class="form-control m-input" value="{{ old('office_name') }}"  placeholder="اسم المكتب ">
                        @if ($errors->has('office_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('office_name') }}</strong>
                            </span>
                        @endif
                    </div>
                  
                    <div class="col-lg-6 col-md-6 {{ $errors->has('event_name') ? ' has-error' : '' }}">
                        <label> اسم المناسبة </label>
                        <input type="text" name="event_name" required class="form-control m-input" value="{{ old('event_name') }}"  placeholder="اسم المناسبة ">
                        @if ($errors->has('event_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('event_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('event_date') ? ' has-error' : '' }}">
                        <label> تاريخ الحفل </label>
                        <input type="text" name="event_date" required class="form-control m-input" value="{{ old('event_date') }}"  placeholder="YYYY-MM-DD" id="flatpickr-date">
                        @if ($errors->has('event_date'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('event_date') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('event_address') ? ' has-error' : '' }}">
                        <label> مكان الحفل </label>
                        <input type="text" name="event_address" required class="form-control m-input" value="{{ old('event_address') }}"  placeholder="مكان الحفل">
                        @if ($errors->has('event_address'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('event_address') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('package_price') ? ' has-error' : '' }}">
                       <label> سعر الباقة </label>
                       <input type="text" name="package_price" required class="form-control m-input" value="{{ old('package_price') }}"  placeholder="سعر الباقة ">
                       @if ($errors->has('package_price'))
                           <span class="help-block" style="color:red">
                               <strong>{{ $errors->first('package_price') }}</strong>
                           </span>
                       @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('events_count') ? ' has-error' : '' }}">
                        <label> عدد الدعوات </label>
                        <input type="text" name="events_count" required class="form-control m-input" value="{{ old('events_count') }}"  placeholder="عدد الدعوات ">
                        @if ($errors->has('events_count'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('events_count') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('gender') ? ' has-error' : '' }}">
                        <label> الجنس <span class="text-danger">*</span>   </label>
                        <select name="gender" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" disabled selected>  أختر رجل / أمرأه </option>
                            <option value="male" @if(old('gender') == 'male') selected @endif> رجل </option>
                            <option value="female" @if(old('gender') == 'female') selected @endif> أمرأه </option>
                        </select>
                        @if ($errors->has('gender'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('gender') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label> رقم الهاتف </label>
                        <input type="text" name="mobile" required onkeypress="return isNumberKey(event)" class="form-control m-input" value="{{ old('mobile') }}"  placeholder="رقم الهاتف ">
                        @if ($errors->has('mobile'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('mobile') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('employees_count') ? ' has-error' : '' }}">
                        <label> عدد الموظفين </label>
                        <input type="text" name="employees_count" required  class="form-control m-input" value="{{ old('employees_count') }}"  placeholder="عدد الموظفين ">
                        @if ($errors->has('employees_count'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('employees_count') }}</strong>
                            </span>
                        @endif
                    </div>

                  	<div class="col-sm-12 {{ $errors->has('image') ? ' has-error' : '' }}">
                        <label> صوره  <span class="text-danger">*</span> </label>
                        <input class="form-control" type="file" id="formFile" required name="image" />
                        @if ($errors->has('image'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('image') }}</strong>
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
