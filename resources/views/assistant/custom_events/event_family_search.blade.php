@extends('assistant.layouts.master')
{{-- title --}}

@section('title','دخول المناسبة ')

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

    .bootstrap-select .dropdown-menu {
        transform: translate(0px, 0px) !important;
    }


    div.dt-buttons {
        margin-bottom: 30px !important
    }

  </style>


@endsection


@section('content')

    <!-- Cards with badge -->
    <div class="row justify-content-center" style="margin-top: 20px">

        <div class="col-12">
            @include('flash-message')
        </div>

        <div class="col-12">
            <div class="card">

              	<div style="padding: 20px;padding-bottom: 0;margin-top: 15px;">
                	<h3>
                    	دخول المناسبة
                    </h3>
                </div>

                <div class="card-body">

                    @foreach ($event_users as $user)

                        <div class="row" style="padding-left: 5.6%;">

                            <div class="col-md-8 col-sm-8 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                                <label> أسم المستخدم  </label>
                                <input type="text" name="old_event_users[{{$user->id}}][name]" required class="form-control m-input" value="{{ $user->name }}"  placeholder="أسم المستخدم">
                                @if ($errors->has('name'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>

                            {{--  <div class="col-md-4 col-sm-4 {{ $errors->has('mobile') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                                <label> رقم الموبيل </label>
                                <input type="text" name="old_event_users[{{$user->id}}][mobile]" onkeypress="return isNumberKey(event)" class="form-control m-input" value="{{ $user->mobile }}"  placeholder="رقم الموبيل">
                                @if ($errors->has('mobile'))
                                <span class="help-block" style="color:red">
                                    <strong>{{ $errors->first('mobile') }}</strong>
                                </span>
                                @endif
                            </div>  --}}

                            <div class="col-md-4" style="margin-top:5px">

                                <span class="btn btn-danger DeletingUser" name="{{ $user->id }}" title='Delete' style="display: inline-block;color: #FFF;cursor:pointer;margin-top: 13px;">
                                    {{ trans('home.delete') }}
                                </span>

                                @if($user->scan_qr == 'no')
                                <button type="button" class="btn btn-success open_event_family" name="{{ $user->id }}" style="margin-bottom: 5px;display: inline-block;margin-top: 17px;">
                                    دخول الحفل
                                </button>
                                @else
                                <button type="button" class="btn btn-primary" style="margin-bottom: 5px;display: inline-block;margin-top: 17px;">
                                    تم الدخول مسبقا
                                </button>
                                @endif
                            </div>



                        </div>

                    @endforeach

                </div>


            </div>
        </div>

    </div>

@endsection





@section('footer')


    <script type="text/javascript">

        $(document).ready(function () {


            $('.DeletingUser').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                        title: "Do you really want to delete this row ?",
                        text: "After deleting this delete row, you cannot go back .",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "yes",
                        cancelButtonText: "no",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            window.location.href = '{{ url('assistant_panel/custom_event_family/destroy') }}' + '/' + ID;

                        }
                    });
            });


            $('.open_event_family').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                        title: "هل تريد تاكيد حضور الحفل",
                        text: "",
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
                            window.location.href = '{{ url('assistant_panel/open_custom_event_family') }}' + '/' + ID;

                        }
                    });
            });



        });


    </script>

@endsection


