@extends('parking.layouts.master')
{{-- title --}}

@section('title','تعديل دخول باركينج')

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

  </style>

@endsection

@section('content')



<!-- Basic Inputs start -->
<section id="basic-input">

  @include('flash-message')


  <div class="row">
    <div class="col-md-12">

      <div class="card">


        <div class="card-header" style="margin-top:10px">

            <div style="margin-top: 20px;">
                <span class="btn btn-primary" onclick="printImg()" style="cursor: pointer;font-weight:bold;color:#FFF">
                    <span>طباعه</span>
                </span>
                @if($Item->mobile != null)
                <span class="btn btn-success" onclick="SentModal()" id="send_btn" style="cursor: pointer;font-weight:bold;color:#FFF">
                    <span>أرسال</span>
                </span>
                @else
                <span class="btn btn-success" disabled style="cursor: pointer;font-weight:bold;color:#FFF;opacity: .5;">
                    <span>أرسال</span>
                </span>
                @endif
            </div>

        </div>


        <!--begin::Form-->
        {!! Form::model($Item, [ 'route' => ['parking_panel.parking.update' , $Item->id ] , 'method' => 'patch', 'role'=>'form','id'=>'edit', 'files' => true ]) !!}

            <div class="card-body">

                <input type="hidden" name="id" value="{{$Item->id}}">

                <div class="row">

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('car_code') ? ' has-error' : '' }}">
                        <label> كود السيارة </label>
                        <input type="text" disabled class="form-control m-input" value="{{ $Item->car_code }}"  placeholder="كود السيارة ">
                        @if ($errors->has('car_code'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('car_code') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('serial_number') ? ' has-error' : '' }}">
                        <label> رقم التسلسل </label>
                        <input type="text" disabled class="form-control m-input" value="{{ $Item->serial_number }}"  placeholder="رقم التسلسل ">
                        @if ($errors->has('serial_number'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('serial_number') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('user_name') ? ' has-error' : '' }}">
                        <label> أسم المستخدمين </label>
                        <input type="text" name="user_name" required class="form-control m-input" value="{{ $Item->user_name }}"  placeholder="أسم المستخدمين ">
                        @if ($errors->has('user_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('user_name') }}</strong>
                            </span>
                        @endif
                    </div>


                  	<div class="col-lg-6 col-sm-6 {{ $errors->has('location') ? ' has-error' : '' }}">
                        <label> مكان الحدث </label>
                        <input type="text" name="location" required class="form-control m-input" value="{{ $Item->location }}"  placeholder="مكان الحدث ">
                        @if ($errors->has('location'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('location') }}</strong>
                            </span>
                        @endif
                    </div>
                  
                  	@php
                        $mobile_codes = App\Models\MobileCodes::get(['id','ar_country_name','code']);
                    @endphp

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('mobile_code') ? ' has-error' : '' }}">
                        <label>  كود الموبيل <span class="text-danger">*</span>   </label>
                        <select name="mobile_code" class="form-control m-bootstrap-select m_selectpicker"  data-live-search="true" required>
                            <option value="" disabled selected>  أختر كود </option>
                            @if($mobile_codes != null && $mobile_codes->count() > 0)
                                @foreach($mobile_codes as $value)
                                <option value="{{ $value->code }}" @if($Item->mobile_code == $value->code) selected @endif> {{ $value->ar_country_name }} ({{ $value->code }}) </option>
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
                        <label> رقم الموبيل </label>
                        <input type="text" name="mobile" required class="form-control m-input" value="{{ $Item->mobile }}"  placeholder="رقم الموبيل ">
                        @if ($errors->has('mobile'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('mobile') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('car_type') ? ' has-error' : '' }}">
                        <label> نوع السياره </label>
                        <input type="text" name="car_type" required class="form-control m-input" value="{{ $Item->car_type }}"  placeholder="نوع السياره ">
                        @if ($errors->has('car_type'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('car_type') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('car_number') ? ' has-error' : '' }}">
                        <label> رقم السياره </label>
                        <input type="text" name="car_number" required class="form-control m-input" value="{{ $Item->car_number }}"  placeholder="رقم السياره ">
                        @if ($errors->has('car_number'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('car_number') }}</strong>
                            </span>
                        @endif
                    </div>

                    

                    <div class="col-md-12" style="margin-bottom: 15px">
                        <button type="submit" form="edit" class="btn btn-primary mr-2">
                            {{ trans('home.update') }}
                        </button>
                    </div>

                </div>

            </div>

            <div class="card-footer" style="padding-left: 25px !important;margin-top: 0px !important;">
                <div class="col-md-12" id='printarea'>
                    <div>
                        <img id="mainImg" src="{{ asset('qr_code/'.$Item->uu_id.'-car-qr.png') }}" style="max-height: 350px">
                    </div>
                </div>
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

    <script type="text/javascript" src="{{ asset('printThis/printThis.js') }}"></script>

    <script>

        $('#print_btn').on('click',function(){
            $('#printarea').printThis();
        });

    </script>

     <script>

        function ImageToPrint2(source)
        {
            return "<html><head><scri"+"pt>function step1(){\n" +
                    "setTimeout('step2()', 10);}\n" +
                    "function step2(){window.print();window.close()}\n" +
                    "</scri" + "pt></head><body onload='step1()'>\n" +
                    "<img src='" + source + "' /></body></html>";
        }

        function PrintImage2(source)
        {
            var Pagelink = "about:blank";
            var pwa = window.open(Pagelink, "_new");
            pwa.document.open();
            pwa.document.write(ImageToPrint(source));
            pwa.document.close();
        }

    </script>

    <script type="text/javascript">
        function printImg() {
        pwin = window.open(document.getElementById("mainImg").src,"_blank");
        //pwin.onload = function () {window.print();}
        }
    </script>

    <script>
        function SentModal() {

            swal({
                title: "",
                text: "هل تريد ارسال رسالة لصاحب هذه السياره رسالة ؟ ",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{ trans('home.yes') }}",
                cancelButtonText: "{{ trans('home.no') }}",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    window.location.href = '{{ url('parking_panel/parking/sent-message') }}' + '/' + {{ $Item->id }};

                }
            });
        }
    </script>

@endsection
