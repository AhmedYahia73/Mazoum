@extends('admin.layouts.master')
{{-- title --}}

@section('title','أضافة باقة جديدة')

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
            أضافة باقة جديدة
          </h4>
        </div>


        {!! Form::open(['url' => "admin/packages", 'role'=>'form','id'=>'add','method'=>'post', 'files' => true]) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('en_name') ? ' has-error' : '' }}">
                        <label> أسم الباقة باللغة الأنجليزية </label>
                        <input type="text" name="en_name" required class="form-control m-input" value="{{ old('en_name') }}"  placeholder="أسم الباقة باللغة الأنجليزية ">
                        @if ($errors->has('en_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('en_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                        <label> أسم الباقة باللغة العربية </label>
                        <input type="text" name="ar_name" required class="form-control m-input" value="{{ old('ar_name') }}"  placeholder="أسم الباقة باللغة العربية ">
                        @if ($errors->has('ar_name'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('ar_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('users_count') ? ' has-error' : '' }}">
                       <label> عدد المستخدمين </label>
                       <input type="text" name="users_count" required onkeypress="return isNumberKey(event)" class="form-control m-input" value="{{ old('users_count') }}"  placeholder="عدد المستخدمين ">
                       @if ($errors->has('users_count'))
                           <span class="help-block" style="color:red">
                               <strong>{{ $errors->first('users_count') }}</strong>
                           </span>
                       @endif
                    </div>

                    <div class="col-lg-6 col-md-6 {{ $errors->has('price') ? ' has-error' : '' }}">
                        <label> سعر الباقة </label>
                        <input type="text" name="price" required class="form-control m-input" value="{{ old('price') }}"  placeholder="سعر الباقة">
                        @if ($errors->has('price'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-12 col-sm-12 {{ $errors->has('currency_id') ? ' has-error' : '' }}" style="margin-top: 20px">
                        <label> العملا <span class="text-danger">*</span>   </label>
                        <select name="currency_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" disabled selected>  أختر عملة </option>
                             @foreach(Currencies() as $key => $value)
                                <option value="{{ $key }}" @if(old('currency_id') == $key) selected @endif>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('currency_id'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('currency_id') }} </strong>
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

@endsection
