@extends('assistant.layouts.master')
{{-- title --}}

@section('title','أضافه دعوه جديدة')

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
            أضافه دعوه جديدة
          </h4>
        </div>


        {!! Form::open(['url' => "assistant_panel/custom_events", 'role'=>'form','id'=>'add','method'=>'post','files' => true]) !!}

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 col-sm-6 {{ $errors->has('assistant_id') ? ' has-error' : '' }}">
                        <label> الموظفين  <span class="text-danger">*</span> </label>
                        <select name="assistant_id" id="assistant_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true">  الموظفين  </option>
                            @foreach (Assistants() as $key => $value)
                                <option value="{{ $key }}" @if(old('assistant_id') == $key) {{ 'selected' }} @endif> {{ $value }} </option>
                            @endforeach
                        </select>
                        @if ($errors->has('assistant_id'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('assistant_id') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-md-6 col-sm-6 {{ $errors->has('user_id') ? ' has-error' : '' }}">
                        <label> المستخدمين  <span class="text-danger">*</span> </label>
                        <select name="user_id" id="user_id" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true">  المستخدمين  </option>
                            @foreach (Users() as $key => $value)
                                <option value="{{ $key }}" @if(old('user_id') == $key) {{ 'selected' }} @endif> {{ $value }} </option>
                            @endforeach
                        </select>
                        @if ($errors->has('user_id'))
                         <span class="help-block" style="color:red">
                             <strong>{{ $errors->first('user_id') }}</strong>
                         </span>
                        @endif
                    </div>


                    <div class="col-lg-6 col-sm-6 {{ $errors->has('title') ? ' has-error' : '' }}">
                        <label> عنوان الدعوه </label>
                        <input type="text" name="title" required class="form-control m-input" value="{{ old('title') }}"  placeholder="عنوان الدعوه ">
                        @if ($errors->has('title'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-md-6 col-sm-6 {{ $errors->has('language') ? ' has-error' : '' }}">
                        <label> اللغه  <span class="text-danger">*</span> </label>
                        <select name="language" id="language" class="form-control m-bootstrap-select m_selectpicker" data-live-search="true" required>
                            <option value="" selected="true"> اللغه </option>
                            <option value="ar" @if(old('language') == 'ar') {{ 'selected' }} @endif> عربي </option>
                            <option value="en" @if(old('language') == 'en') {{ 'selected' }} @endif> انجليزي </option>
                        </select>
                        @if ($errors->has('language'))
                         <span class="help-block" style="color:red">
                             <strong>{{ $errors->first('language') }}</strong>
                         </span>
                        @endif
                    </div>

                    <div class="col-sm-6 {{ $errors->has('image') ? ' has-error' : '' }}">
                        <label> صوره  <span class="text-danger">*</span> </label>
                        <input class="form-control" type="file" id="formFile" required name="image" />
                        @if ($errors->has('image'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>

                  	<div class="col-lg-6 col-sm-6 {{ $errors->has('color') ? ' has-error' : '' }}">
                        <label> اللون  </label>
                        <input type="color" name="color" required class="form-control m-input" value="{{ old('color') }}"  placeholder=" اللون ">
                        @if ($errors->has('color'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('color') }}</strong>
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


