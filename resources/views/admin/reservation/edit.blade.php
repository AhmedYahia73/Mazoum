@extends('admin.layouts.master')
{{-- title --}}

@section('title','تعديل باقة معزوم')


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
            تعديل باقة معزوم
          </h4>
        </div>
        
        <div style="margin: 20px;">
            <b>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessage">
					الدفع
                </button>
            </b>
          	<b>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessageV2">
					ارسال بيانات الحجز الي العميل
                </button>
            </b>
        </div>


        <!--begin::Form-->
        {!! Form::model($Item, [ 'route' => ['admin.reservation.update' , $Item->id ] , 'method' => 'patch', 'role'=>'form','id'=>'edit', 'files' => true ]) !!}

            <div class="card-body">

                <input type="hidden" name="id" value="{{$Item->id}}">

                <div class="row">
                  
                  
                  	<div class="col-lg-6 col-md-6 {{ $errors->has('employee_name') ? ' has-error' : '' }}">
                        <label> اسم الموظف </label>
                        <input type="text" name="employee_name" required class="form-control m-input" value="{{ $Item->employee_name }}"  placeholder="اسم الموظف ">
                        @if ($errors->has('employee_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('employee_name') }}</strong>
                            </span>
                        @endif
                    </div>
                  
                  	<div class="col-lg-6 col-md-6 {{ $errors->has('office_name') ? ' has-error' : '' }}">
                        <label> اسم المكتب </label>
                        <input type="text" name="office_name" required class="form-control m-input" value="{{ $Item->office_name }}"  placeholder="اسم المكتب ">
                        @if ($errors->has('office_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('office_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('event_name') ? ' has-error' : '' }}">
                        <label> اسم المناسبة </label>
                        <input type="text" name="event_name" required class="form-control m-input" value="{{ $Item->event_name }}"  placeholder="اسم المناسبة ">
                        @if ($errors->has('event_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('event_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('event_date') ? ' has-error' : '' }}">
                        <label> تاريخ الحفل </label>
                        <input type="text" name="event_date" required class="form-control m-input" value="{{ $Item->event_date }}"  placeholder="YYYY-MM-DD" id="flatpickr-date">
                        @if ($errors->has('event_date'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('event_date') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('event_address') ? ' has-error' : '' }}">
                        <label> مكان الحفل </label>
                        <input type="text" name="event_address" required class="form-control m-input" value="{{ $Item->event_address }}"  placeholder="مكان الحفل">
                        @if ($errors->has('event_address'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('event_address') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('package_price') ? ' has-error' : '' }}">
                       <label> سعر الباقة </label>
                       <input type="text" name="package_price" required  class="form-control m-input" value="{{ $Item->package_price }}"  placeholder="سعر الباقة ">
                       @if ($errors->has('package_price'))
                           <span class="help-block" style="color:red">
                               <strong>{{ $errors->first('package_price') }}</strong>
                           </span>
                       @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('events_count') ? ' has-error' : '' }}">
                        <label> عدد الدعوات </label>
                        <input type="text" name="events_count" required  class="form-control m-input" value="{{ $Item->events_count }}"  placeholder="عدد الدعوات ">
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
                            <option value="male" @if($Item->gender == 'male') selected @endif> رجل </option>
                            <option value="female" @if($Item->gender == 'female') selected @endif> أمرأه </option>
                        </select>
                        @if ($errors->has('gender'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('gender') }} </strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label> رقم الهاتف </label>
                        <input type="text" name="mobile" required onkeypress="return isNumberKey(event)" class="form-control m-input" value="{{ $Item->mobile }}"  placeholder="رقم الهاتف ">
                        @if ($errors->has('mobile'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('mobile') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('employees_count') ? ' has-error' : '' }}">
                        <label> عدد الموظفين </label>
                        <input type="text" name="employees_count" required  class="form-control m-input" value="{{ $Item->employees_count }}"  placeholder="عدد الموظفين ">
                        @if ($errors->has('employees_count'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('employees_count') }}</strong>
                            </span>
                        @endif
                    </div>

                  	<div class="col-sm-12 {{ $errors->has('image') ? ' has-error' : '' }}">
                        <label> صوره    </label>
                        <input class="form-control" type="file" id="formFile" name="image" />

                      	<img id="imgPreview" src="{{ $Item->image }}?{{rand()}}" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>

                        @if ($errors->has('image'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>

                </div>

            </div>

            <div class="card-footer" style="padding-left: 25px !important;margin-top: 0px !important;">
                <button type="submit" form="edit" class="btn btn-primary mr-2">
                    {{ trans('home.update') }}
                </button>
            </div>

       {!! Form::close() !!}
        <!--end::Form-->
        
        
        
      {!! Form::open(['url' => "admin/send_reservation_to_paid",'role'=>'form','id'=>'send_form','method'=>'post']) !!}

        <input type="hidden" name="reservation_id" value="{{ $Item->id }}">
        
        <div class="modal fade" id="SendCustomMessage" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                
                <div class="modal-header">
                  <h5 class="modal-title" id="SendCustomMessageLabel">  رسالة خاصة </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                  
                  <div class="{{ $errors->has('mobile') ? ' has-error' : '' }}" style="margin-bottom:25px">
                    <label> رقم الهاتف </label>
                    <input type="text" name="mobile" required onkeypress="return isNumberKey(event)" class="form-control m-input" value="{{ $Item->mobile }}"  placeholder="رقم الهاتف ">
                    @if ($errors->has('mobile'))
                    <span class="help-block" style="color:red">
                      <strong>{{ $errors->first('mobile') }}</strong>
                    </span>
                    @endif
                  </div>

                  
                  <div class="">
                      <label> محتوي الرسالة </label>
                      <textarea name="message" rows="5" class="form-control" placeholder="محتوي الرسالة">{{ old('message') }}</textarea>
                  </div>
                </div>
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                  <button type="button" onclick="sendMessageToSelected()" class="btn btn-primary"> أرسال </button>
                </div>
                
              </div>
            </div>
        </div>

    {!! Form::close() !!}
        
        
        {!! Form::open(['url' => "admin/send_reservation_info_to_user",'role'=>'form','id'=>'send_form2','method'=>'post']) !!}

        <input type="hidden" name="reservation_id" value="{{ $Item->id }}">
        
        <div class="modal fade" id="SendCustomMessageV2" tabindex="-1" aria-labelledby="SendCustomMessageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                
                <div class="modal-header">
                  <h5 class="modal-title" id="SendCustomMessageLabel">   
                    ارسال بيانات الحجز الي العميل
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                  
                  <div class="{{ $errors->has('mobile') ? ' has-error' : '' }}" style="margin-bottom:25px">
                    <label> رقم الهاتف </label>
                    <input type="text" disabled onkeypress="return isNumberKey(event)" class="form-control m-input" value="{{ $Item->mobile }}"  placeholder="رقم الهاتف ">
                    @if ($errors->has('mobile'))
                    <span class="help-block" style="color:red">
                      <strong>{{ $errors->first('mobile') }}</strong>
                    </span>
                    @endif
                  </div>


                </div>
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">أغلاق</button>
                  <button type="button" onclick="sendMessageToSelectedV2()" class="btn btn-primary"> أرسال </button>
                </div>
                
              </div>
            </div>
        </div>

    {!! Form::close() !!}



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


	<script>

        function sendMessageToSelected() {
            swal({
                title: "هل تريد حقًا ارسال رسالة",
                text: "بعد الأرسال ، لا يمكنك العودة.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                closeOnConfirm: true,
                closeOnCancel: true
              },
              function (isConfirm) {
                if (isConfirm) {
                  //$('#send_form').attr('action', '{{ url("admin/send_reservation_to_paid") }}').submit();
                  $('#send_form').submit();
                }
              });
        }

    </script>


	<script>

        function sendMessageToSelectedV2() {
            swal({
                title: "هل تريد حقًا ارسال بيانات الحجز الي العميل",
                text: "بعد الأرسال ، لا يمكنك العودة.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "نعم",
                cancelButtonText: "لا",
                closeOnConfirm: true,
                closeOnCancel: true
              },
              function (isConfirm) {
                if (isConfirm) {
                  //$('#send_form2').attr('action', '{{ url("admin/send_reservation_info_to_user") }}').submit();
                  $('#send_form2').submit();
                }
              });
        }

    </script>

@endsection
