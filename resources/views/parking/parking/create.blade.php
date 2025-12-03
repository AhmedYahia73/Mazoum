@extends('parking.layouts.master')
{{-- title --}}

@section('title','أضافة دخول باركينج')

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

        <div class="card-header" style="margin-bottom: 10px;margin-top:10px">
          <h4 class="card-title">
            أضافة دخول باركينج
          </h4>
        </div>


        {!! Form::open(['url' => "parking_panel/parking", 'role'=>'form','id'=>'add','method'=>'post', 'files' => true]) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-12 col-sm-12 {{ $errors->has('car_code') ? ' has-error' : '' }}" style="margin-top: 20px">
                        <label> أكواد السياره <span class="text-danger">*</span>   </label>
                        <select name="car_code" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" disabled selected>  أختر كود </option>
                            @for ($i=1;$i<=1000;$i++)
                                <option value="{{ $i }}" @if(old('car_code') == $i) selected @endif>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @if ($errors->has('car_code'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('car_code') }} </strong>
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
