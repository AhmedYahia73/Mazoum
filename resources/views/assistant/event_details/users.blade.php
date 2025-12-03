@extends('assistant.layouts.master')
{{-- title --}}

@section('title',$title)

@section('header')


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
          <b>
            <button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 0px;">
              حذف المستخدمين
            </button>
          </b>
        </div>
      
      	{!! Form::open(['role'=>'form','id'=>'all_users','method'=>'post']) !!}


        <input type="hidden" name="event_id" value="{{ $Item->id }}">

        <div class="col-12">
            <div class="card"  style="padding: 20px">

                <h3>
                    {{ $title }}
                </h3>

                <div>

                    <div class="table-responsive">

                        <table class="table zero-configuration">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">عدد الدعوات</th>
                                    <th scope="col"> أسم المستخدم </th>
                                    <th scope="col">رقم الهاتف </th>
                                    <th scope="col"  style="text-align: center;">الحالة</th>
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
                                                 <input type="checkbox" class="check_items" name="users[{{$x}}][id]" value="{{ $user->id }}" id="user{{ $user->id }}" style="cursor: pointer;margin-right: 5px;width: 20px;height: 20px;">
                                              </div>
                                            </th>

                                            <td>
                                                {{ $user->users_count }}
                                            </td>

                                            <td>
                                                <label for="user{{ $user->id }}" style="cursor: pointer">
                                                    {{ $user->name }}
                                                </label>
                                            </td>

                                            <td style="direction: ltr;">
                                                {{ $user->mobile }}
                                            </td>

                                            <td style="text-align: center;">
                                                {{ $user->status }}
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
                <button type="button" onclick="return sendToSelected();" class="btn btn-success" style="margin-left:0px;margin-bottom: 30px;">
                    أرسال
                </button>
              	<button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 30px;">
                    حذف المستخدمين
                </button>
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
                  $('#all_users').attr('action', '{{ url("assistant_panel/send_event_users") }}').submit();
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
                  $('#all_users').attr('action', '{{ url("assistant_panel/delete_event_users") }}').submit();
                }
              });
          }

    </script>

@endsection



