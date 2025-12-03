@extends('admin.layouts.master')
{{-- title --}}

@section('title','زوار الحدث')

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
                    	زوار الحدث  
                    </h3>
                </div>

                <div class="card-body">
                    
                  <!--begin::Form-->
                  {!! Form::open(['url' => "admin/update_event_users",'role'=>'form','id'=>'update_event_users','method'=>'post']) !!}

                  
                      <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" form="update_event_users" class="btn btn-success" style="width:100%;margin-left:0px;margin-bottom: 30px;">
                                تحديث
                            </button>
                        </div>
                      </div>
                  
                      <input type="hidden" value="{{ $event_id }}" name="event_id">

                      @foreach ($event_users as $user)

                          <div class="row" style="padding-left: 5.6%;">

                              <div class="col-md-2 col-sm-2 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                                <label> زوار الحدث </label>  
                                <input type="text" name="old_event_users[{{$user->id}}][users_count]" value="{{ $user->users_count ? $user->users_count : 1 }}"  onkeypress="return isNumberKey(event)" class="form-control" style="max-width:70px;text-align:center;cursor: pointer">
                              </div>

                              <div class="col-md-4 col-sm-4 {{ $errors->has('name') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                                  <label> أسم المستخدم  </label>
                                  <input type="text" name="old_event_users[{{$user->id}}][name]" required class="form-control m-input" value="{{ $user->name }}"  placeholder="أسم المستخدم">
                                  @if ($errors->has('name'))
                                  <span class="help-block" style="color:red">
                                      <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                                  @endif
                              </div>

                              <div class="col-md-4 col-sm-4 {{ $errors->has('mobile') ? ' has-error' : '' }}" style="margin-bottom: 20px">
                                  <label> رقم الموبيل </label>
                                  <input type="text" name="old_event_users[{{$user->id}}][mobile]" onkeypress="return isNumberKey(event)" required class="form-control m-input" value="{{ $user->mobile }}"  placeholder="رقم الموبيل">
                                  @if ($errors->has('mobile'))
                                  <span class="help-block" style="color:red">
                                      <strong>{{ $errors->first('mobile') }}</strong>
                                  </span>
                                  @endif
                              </div>

                              <div class="col-md-2" style="margin-top:5px">
                                  <span class="btn btn-danger DeletingUser" name="{{ $user->id }}" title='Delete' style="display: block;color: #FFF;cursor:pointer;margin-top: 17px;">
                                      {{ trans('home.delete') }}
                                  </span>
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
                            window.location.href = '{{ url('admin/event_users/destroy') }}' + '/' + ID;

                        }
                    });
            });
          

        });


    </script>

@endsection


