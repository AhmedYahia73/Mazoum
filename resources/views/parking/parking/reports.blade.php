@extends('parking.layouts.master')
{{-- title --}}

@section('title','التقارير')

@section('header')

  <style>
    th , td {
      text-align: center !important
    }

    .is_popularity i {
        color: blue
    }
  </style>

@endsection



@section('content')




<!-- Zero configuration table -->
<section id="basic-datatable">

  @include('flash-message')

   {!! Form::open(['url' => "parking_panel/delete_selected_items", 'role'=>'form','id'=>'delete_selected_items','method'=>'get']) !!}

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header" style="margin-bottom: 30px">
          <h4 class="card-title">
            التقارير
          </h4>
        </div>
        
        <div class="card-header" style="margin-bottom:20px">
          <b>
            <label class="btn btn-primary">
              <input type="checkbox" for="checkbox_select" id="select_all" name="select_all">
              <span>
                أختيار الكل
              </span>
            </label>
          </b>

          <button type="button" id="delete_selected_items_btn" class="btn btn-danger mr-2">
            حذف العناصر المختارة
          </button>

        </div>

        <div class="card-body card-dashboard">

          <div class="">

            <table class="table" id="report_table">

              <thead>
                <tr>
                    <th>#</th>
                    <th> أسم المستخدم </th>
                    <th> رقم التسلسل </th>
                    <th> رقم الموبيل </th>
                    <th> رقم / كود السياره </th>
                    <th> نوع السياره </th>
                    <th> وقت الدخول </th>
                    <th> وقت الخروج </th>
                    <th> الأدوات </th>
                </tr>
              </thead>

              <tbody>

                @php $x = 1; @endphp

                @foreach($Item as $value)

                  <tr>
                      <td>
                            <input class="check_items" type="checkbox" value="{{ $value->id }}" name="items[]" />
                      </td>
                      <td>
                        {{ $value->user_name }}
                      </td>
                      <td>
                        {{ $value->serial_number }}
                      </td>
                      <td>
                        {{ $value->mobile }}
                      </td>
                      <td>
                        {{ $value->car_number }} /  {{ $value->car_code }}
                      </td>
                      <td>
                        {{ $value->car_type }}
                      </td>
                      <td>
                        {{ $value->entry_time != null ? Carbon\Carbon::parse($value->entry_time)->format('Y-m-d h:i A') : null }}
                      </td>
                      <td>
                        {{ $value->out_time != null ? Carbon\Carbon::parse($value->out_time)->format('Y-m-d h:i A') : null }}
                      </td>
                    	<td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a onclick="return DeletingModal({{ $value->id }});" class="dropdown-item DeletingModal"name="{{ $value->id }}" href="javascript:void(0);">
                                <i class="bx bx-trash me-1"></i> {{ trans('home.Delete') }}
                              </a>
                          </div>
                        </div>
                      </td>
                  </tr>

                  @php $x = $x + 1; @endphp

                @endforeach


              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  {!! Form::close() !!}
  
</section>
<!--/ Zero configuration table -->



@endsection



@section('footer')

	<script>
        'use strict';
        $(function () {

            var dt_responsive_table = $('#report_table');

            if (dt_responsive_table.length) {
                var dt_responsive = dt_responsive_table.DataTable({
                    responsive: 0,
                    paging: 0,
                    iDisplayLength: 1000,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy','excel','print'
                    ],
                });
            }
        });
    </script>


	    <script>

        function DeletingModal(ID) {
            swal({
                title: "{{ trans('home.delete_msg1') }}",
                text: "{{ trans('home.delete_msg2') }}",
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
                    window.location.href = '{{ url('parking_panel/reports/destroy') }}' + '/' + ID;

                }
            });
        }

    </script>

    <script>

        $(document).ready(function () {

            $('.DeletingModal').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                    title: "{{ trans('home.delete_msg1') }}",
                    text: "{{ trans('home.delete_msg2') }}",
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
                        window.location.href = '{{ url('parking_panel/reports/destroy') }}' + '/' + ID;

                    }
                });
            });

        });

    </script>

	<script>

        $(document).ready(function () {

            $('#select_all').click(function () {
               $('.check_items').prop('checked', this.checked);
            });

            $('#delete_selected_items_btn').click(function() {

                swal({
                        title: "هل تريد حذف الصفوف المختارة ",
                        text: "بعد الحذف لا يمكنك العودة.",
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
                            $('#delete_selected_items').submit();
                        }
                    }
                );
            })

        });

    </script>

@endsection
