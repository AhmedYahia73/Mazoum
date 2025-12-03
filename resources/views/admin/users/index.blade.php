@extends('admin.layouts.master')
{{-- title --}}

@section('title',trans('home.users'))

@section('header')

  <style>
    th , td {
      text-align: center !important
    }

    .is_popularity i {
        color: blue
    }

    [dir=rtl] table.dataTable thead th, [dir=rtl] table.dataTable tbody td, [dir=rtl] table.dataTable tfoot th {
        padding-right: 15px;
        padding-left: 15px;
    }
  </style>

@endsection



@section('content')

<div class="row">
  <div class="col-12">
    <p>
      <a href="{{asset('admin/users/create')}}" id="addRow" class="btn btn-primary">
        <i class="bx bx-plus"></i>&nbsp;
        {{ trans('home.add_user') }}
      </a>
    </p>
  </div>
</div>



<!-- Zero configuration table -->
<section id="basic-datatable">

  @include('flash-message')

  <div class="row">
    <div class="col-12">

      {!! Form::open(['url' => "admin/delete_selected_users", 'role'=>'form','id'=>'delete_selected_items','method'=>'post']) !!}

      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            {{ trans('home.users') }}
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

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th style="text-align: right !important">  {{ trans('home.name') }}  </th>
                    <th> تاريخ الأنشاء </th>
                    <th>  {{ trans('home.mobile') }}  </th>
                    <th>  الباقة الحالية </th>
                    <th>  الرصيد </th>
                    <th> {{ trans('home.tools') }}  </th>
                </tr>
              </thead>

              <tbody>

                @php $x = 1; @endphp

                @foreach($Item as $value)

                  <tr>
                    <td>
                      <span style="font-size: 18px;"> {{ $x }} </span>
                      <input class="check_items" type="checkbox" value="{{ $value->id }}" name="items[]" style="width: 17px;height: 17px;" />
                    </td>

                    <td style="text-align: right !important"> {{ $value->name }} </td>

                    <td> {{ Carbon\Carbon::parse($value->created_at)->format('Y-m-d h:i A') }} </td>

                    <td style="direction: ltr;">
                        {{  $value->mobile_code }}
                        {{ $value->mobile }}
                    </td>

                    <td> {{ $value->offer != null ? $value->offer->ar_name : '' }} </td>

                    <td> {{ $value->balance }} </td>

                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('admin/users/'). '/' . $value->id . '/edit'}}">
                                <i class="bx bx-edit-alt me-1"></i> {{ trans('home.Edit') }}
                            </a>
                            <a onclick="return DeletingModal({{ $value->id }});" class="dropdown-item DeletingModal" name="{{ $value->id }}" href="javascript:void(0);">
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

      {!! Form::close() !!}


    </div>
  </div>
</section>
<!--/ Zero configuration table -->



@endsection

{{-- vendor scripts --}}
@section('footer')


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
                window.location.href = '{{ url('admin/users/destroy') }}' + '/' + ID;
            }
        });
    }

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
