@extends('admin.layouts.master')
{{-- title --}}

@section('title','تعديل باقة')

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
        <div class="card-header">
          <h4 class="card-title">
            تعديل باقة
          </h4>
        </div>


        <!--begin::Form-->
        {!! Form::model($Item, [ 'route' => ['admin.pricing.update' , $Item->id ] , 'method' => 'patch', 'role'=>'form','id'=>'edit', 'files' => true ]) !!}

            <div class="card-body">

                <input type="hidden" name="id" value="{{$Item->id}}">

                <div class="row">

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('en_title') ? ' has-error' : '' }}">
                        <label> أسم الباقة باللغة الأنجليزية </label>
                        <input type="text" name="en_title" required class="form-control m-input" value="{{ $Item->en_title }}"  placeholder="أسم الباقة باللغة الأنجليزية ">
                        @if ($errors->has('en_title'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('en_title') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('ar_title') ? ' has-error' : '' }}">
                        <label> أسم الباقة باللغة العربية </label>
                        <input type="text" name="ar_title" required class="form-control m-input" value="{{ $Item->ar_title }}"  placeholder="أسم الباقة باللغة العربية ">
                        @if ($errors->has('ar_title'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('ar_title') }}</strong>
                            </ssend_invitationpan>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('price') ? ' has-error' : '' }}">
                        <label> السعر </label>
                        <input type="text" name="price" required class="form-control m-input" value="{{ $Item->price }}"  placeholder=" السعر ">
                        @if ($errors->has('price'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('price') }}</strong>
                            </ssend_invitationpan>
                        @endif
                    </div>

                    <div class="col-lg-6 col-sm-6 {{ $errors->has('users_count') ? ' has-error' : '' }}">
                        <label> عدد الأشخاص </label>
                        <input type="text" name="users_count" required class="form-control m-input" value="{{ $Item->users_count }}"  placeholder=" عدد الأشخاص ">
                        @if ($errors->has('users_count'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('users_count') }}</strong>
                            </ssend_invitationpan>
                        @endif
                    </div>

                    <div class="col-md-12">
                        <div style="margin-bottom: 10px">
                            <input type="checkbox" name="send_invitation" id="send_invitation" {{ $Item->send_invitation == 'yes' ? 'checked' : '' }} value="yes" style="cursor: pointer">
                            <label for="send_invitation"  style="cursor: pointer"> أرسـال الدعوات  </label>
                        </div>
                        <div style="margin-bottom: 10px">
                            <input type="checkbox" name="confirm_attendance" id="confirm_attendance" {{ $Item->confirm_attendance == 'yes' ? 'checked' : '' }} value="yes" style="cursor: pointer">
                            <label for="confirm_attendance"  style="cursor: pointer"> تأكيد الحضـور   </label>
                        </div>
                        <div style="margin-bottom: 10px">
                            <input type="checkbox" name="confirm_apology" id="confirm_apology" {{ $Item->confirm_apology == 'yes' ? 'checked' : '' }} value="yes" style="cursor: pointer">
                            <label for="confirm_apology"  style="cursor: pointer"> تأكيد الاعتذار   </label>
                        </div>
                        <div style="margin-bottom: 10px">
                            <input type="checkbox" name="reminder_before_invitation" id="reminder_before_invitation" {{ $Item->reminder_before_invitation == 'yes' ? 'checked' : '' }} value="yes" style="cursor: pointer">
                            <label for="reminder_before_invitation"  style="cursor: pointer">  تذكير قبل الحفـل  </label>
                        </div>
                        <div style="margin-bottom: 10px">
                            <input type="checkbox" name="party_employee" id="party_employee" {{ $Item->party_employee == 'yes' ? 'checked' : '' }} value="yes" style="cursor: pointer">
                            <label for="party_employee"  style="cursor: pointer"> موظف للحفل   </label>
                        </div>
                        <div style="margin-bottom: 10px">
                            <input type="checkbox" name="attendance_report_after_invitation" id="attendance_report_after_invitation" {{ $Item->attendance_report_after_invitation == 'yes' ? 'checked' : '' }} value="yes" style="cursor: pointer">
                            <label for="attendance_report_after_invitation"  style="cursor: pointer"> تقرير بالحضور بعد الحفل </label>
                        </div>
                        <div style="margin-bottom: 10px">
                            <input type="checkbox" name="send_congratulations_after_invitation" id="send_congratulations_after_invitation" {{ $Item->send_congratulations_after_invitation == 'yes' ? 'checked' : '' }} value="yes" style="cursor: pointer">
                            <label for="send_congratulations_after_invitation"  style="cursor: pointer"> إرسال تهنئة بعد الحفل </label>
                        </div>
                      	<div style="margin-bottom: 10px">
                            <input type="checkbox" name="congratulations_messages" id="congratulations_messages" {{ $Item->congratulations_messages == 'yes' ? 'checked' : '' }} value="yes" style="cursor: pointer">
                            <label for="congratulations_messages"  style="cursor: pointer"> رسائل تهنئة المناسبة    </label>
                        </div>
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



      </div>
    </div>
  </div>
</section>
<!-- Basic Inputs end -->



@endsection



