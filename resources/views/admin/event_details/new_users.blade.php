@extends('admin.layouts.master')
{{-- title --}}

@section('title',$title)

@section('header')

    <style>
        td , th {
            text-align:center !important
        }

        td label {
            display: block;
            text-align: right;
        }

        .new_send_user_row {
            background-color: #fdac41 !important;
        }

         .new_send_user_row  td {
            color: #FFF !important
        }
    </style>

@endsection


@section('content')

    <!-- Cards with badge -->
    <div class="row justify-content-center" style="margin-top: 20px">

        <div class="col-12">
            @include('flash-message')
        </div>

      	<div class="col-12" style="margin-bottom: 20px">
          <b>
              <label class="btn btn-warning">
                  <input type="checkbox" for="checkbox_select" id="select_all" name="select_all">
                  <span style="display: inline-block;padding-right:10px">
                      أختر الكل
                  </span>
              </label>
          </b>

          @if(isset($type) && $type != 'confirmed_event_details')
          <b>
            <button type="button" onclick="return sendToSelected();" class="btn btn-success">
              <span> أرسال </span>
              <i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
            </button>
          </b>

          <b>
            <button type="button" class="new_send_btn btn btn-warning mr-2">
                أرسال حديث
            </button>
          </b>
          @endif

          <b>
            <button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 0px;">
              حذف المستخدمين
            </button>
          </b>

          <b>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessage">
                تذكير
            </button>
          </b>


        </div>

      	{!! Form::open(['role'=>'form','id'=>'all_users','method'=>'post','files' => true]) !!}


            <input type="hidden" name="event_id" value="{{ $Item->id }}">

            <div class="col-12">
                <div class="card"  style="padding: 20px">

                    <h3>
                        {{ $title }}
                    </h3>

                    <div>

                        <div class="">

                            <table class="table" id="all_users_table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col"> أسم المستخدم </th>
                                        <th scope="col">رقم الهاتف </th>
                                      	<th scope="col"> تاريخ ووقت التاكيد </th>
                                      <th scope="col"> عدد الدعوات </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php $x = 1; @endphp

                                    @if($data != null && $data->count() > 0)

                                        @foreach ($data as $user)

                                            <tr>
                                                <th scope="row">
                                                    <div style="display: flex;align-items: center;justify-content: center;">
                                                    <span style="font-size: 20px;"> {{ $x }} </span>
                                                    <input type="checkbox" class="check_items" name="users[{{$x}}][id]" value="{{ $user->event_user_id }}" id="user{{ $user->id }}" style="cursor: pointer;margin-right: 5px;width: 20px;height: 20px;">
                                                </div>
                                                </th>

                                                <td>
                                                    <label for="user{{ $user->id }}" style="cursor: pointer">
                                                        {{ @$user->event_user->name }}
                                                    </label>
                                                </td>

                                                <td style="direction: ltr;">
                                                   <span> {{ @$user->event_user->mobile }} </span>
                                                </td>

                                                <td>
												   {{ Carbon\Carbon::parse($user->created_at)->format('Y-m-d h:i A') }}
                                                </td>

                                              	<td>
                                                   <span> {{ @(App\Models\EventUserActions::where('event_id',$user->event_id)->where('event_user_id',$user->event_user_id)->where('action','accept_event')->first())->users_count }} </span>
                                                </td>

                                            </tr>

                                            @php $x = $x + 1; @endphp

                                        @endforeach

                                    @else
                                    <tr>
                                        <td colspan="5">
                                            لا يوجد مستخدمين
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

            <div class="row"  style="margin-top: 20px">
                <div class="col-lg-12">

                    @if(isset($type) && $type != 'confirmed_event_details')
                    <button type="button" onclick="return sendToSelected();" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                        <span> أرسال </span>
                    	<i class="fa fa-paper-plane" aria-hidden="true" style="display: inline-block;margin-right: 5px;"></i>
                    </button>
                    <b>
                      <button type="button" class="new_send_btn btn btn-warning mr-2" style="margin-left:0px;margin-bottom: 30px;">
                          أرسال حديث
                      </button>
                    </b>
                    @endif

                    <button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 30px;">
                        حذف المستخدمين
                    </button>

                    <b>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#SendCustomMessage" style="margin-left:0px;margin-bottom: 30px;">
                            تذكير
                        </button>
                    </b>

                </div>
            </div>


            <div class="modal fade" id="SendCustomMessage" tabindex="-1" aria-labelledby="SendRememberMessageLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="SendRememberMessageLabel">  تذكير   </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div style="margin-bottom: 25px;display: flex;justify-content: start;align-items: center;">
                                <input type="radio" id="old_send2" name="sending_type2" value="old_send" checked style="width: 17px;height: 17px;">
                                <label for="old_send2" style="cursor: pointer;margin-right: 5px;">  أرسال ميتا   </label>

                                <input type="radio" id="new_send2" name="sending_type2" value="new_send" style="width: 17px;height: 17px;margin-right: 30px;">
                                <label for="new_send2" style="cursor: pointer;margin-right: 5px;">أرسال </label>
                            </div>

                          	<div style="margin-bottom: 25px">
                                <label> التاريخ </label>
                                <input type="text" name="date" class="form-control" value="{{ old('date') }}" placeholder="التاريخ">
                            </div>

                          	<div style="margin-bottom: 25px">
                                <label> الوقت </label>
                                <input type="text" name="time" class="form-control" value="{{ old('time') }}" placeholder="الوقت">
                            </div>

                            <div style="margin-bottom: 25px">
                                <label> محتوي الرسالة </label>
                                <textarea name="message2" rows="5" class="form-control" placeholder="محتوي الرسالة">{{ old('message2') }}</textarea>
                            </div>

                            <div class="" style="margin-top: 20px">
                                <label> صوره </label>
                                <input class="form-control" type="file" id="formFile" name="file2" />
                                <img id="imgPreview" src="{{ $Item->file }}?{{rand()}}" style="max-width: 200px;max-height: 200px;margin-bottom: 20px;margin-top: 20px"/>
                                @if ($errors->has('image2'))
                                    <span class="help-block" style="color:red">
                                        <strong>{{ $errors->first('image2') }}</strong>
                                    </span>
                                @endif
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


    </div>

@endsection





@section('footer')

    <script type="text/javascript">

        $(document).ready(function () {

            $('#select_all').click(function () {
               $('.check_items').prop('checked', this.checked);
            });


            $('.new_send_btn').click(function() {

                swal({
                        title: "هل تريد تاكيد أرسال الدعوات لهولاء المستخدمين ",
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
                            $('#all_users').attr('action','{{ asset('admin/new-send-event-invitation') }}').attr('method','post').submit();
                        }
                    }
                );
            });




        });

    </script>

	<script>

        function sendToSelected() {
            swal({
                title: "هل تريد حقًا ارسال دعوه مره اخري",
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
                  $('#all_users').attr('action', '{{ url("admin/send_event_users") }}').submit();
                }
              });
        }



      	function deleteToSelected() {
            swal({
                title: "هل تريد حقًا حذف المستخدمين المختارين",
                text: "بعد الحذف ، لا يمكنك العودة.",
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
                  $('#all_users').attr('action', '{{ url("admin/delete_event_users") }}').submit();
                }
              });
        }



        function sendMessageToSelected() {
            swal({
                title: "هل تريد حقًا تاكيد أرسال تذكير",
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
                  $('#all_users').attr('action', '{{ url("admin/remember-users-to-event") }}').submit();
                }
              });
        }



    </script>

	 <script>
        'use strict';
        $(function () {

          	var all_users_table = $('#all_users_table');

            if (all_users_table.length) {
                var dt_responsive4 = all_users_table.DataTable({
                    responsive: 0,
                    paging: 0,
                    iDisplayLength: 1000,
                    dom: 'Bfrtip',
                    buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    }
                ],
                    "bSort": false
                });
            }



        });
    </script>



@endsection



