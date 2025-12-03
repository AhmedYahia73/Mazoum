@extends('admin.layouts.master')
{{-- title --}}

@section('title','أضافة تصميم جديد')

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
            أضافة تصميم جديد
          </h4>
        </div>


        {!! Form::open(['url' => "admin/uses", 'role'=>'form','id'=>'add','method'=>'post', 'files' => true]) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('en_desc') ? ' has-error' : '' }}">
                        <label> الوصف باللغة الأنجليزية </label>
                        <textarea name="en_desc" required class=" form-control" rows="10" placeholder="الوصف باللغة الأنجليزية">{{ old('en_desc') }}</textarea>
                        @if ($errors->has('en_desc'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('en_desc') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('ar_desc') ? ' has-error' : '' }}">
                        <label> الوصف باللغة العربية </label>
                        <textarea name="ar_desc" required class=" form-control" rows="10" placeholder="الوصف باللغة العربية">{{ old('ar_desc') }}</textarea>
                        @if ($errors->has('ar_desc'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('ar_desc') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-12 col-sm-12 {{ $errors->has('link') ? ' has-error' : '' }}">
                        <label> الرابط </label>
                        <input type="text" name="link" required class="form-control m-input" value="{{ old('link') }}"  placeholder="الرابط ">
                        @if ($errors->has('link'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('link') }}</strong>
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


