@extends('parking.layouts.master')
{{-- title --}}

@section('title','دخول باركينج')

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

<div class="row">
  <div class="col-12">
    <p>
      <a href="{{asset('parking_panel/parking/create')}}" id="addRow" class="btn btn-primary">
        <i class="bx bx-plus"></i>&nbsp;
        أضافة دخول باركينج
      </a>
    </p>
  </div>
</div>



<!-- Zero configuration table -->
<section id="basic-datatable">

  @include('flash-message')

  <div class="row">
    <div class="col-12">
      <div class="card">

        <div class="card-header" style="margin-bottom: 30px">
          <h4 class="card-title">
            دخول باركينج
          </h4>
        </div>

        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> أسم المستخدم </th>
                    <th> رقم التسلسل </th>
                    <th> رقم الموبيل </th>
                    <th> رقم / كود السياره </th>
                    <th> نوع السياره </th>
                    <th> وقت الدخول </th>
                    <th> {{ trans('home.tools') }} </th>
                </tr>
              </thead>

              <tbody>

                @php $x = 1; @endphp

                @foreach($Item as $value)

                  <tr>
                      <td> {{ $x }} </td>
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
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('parking_panel/parking/'). '/' . $value->id . '/edit'}}">
                                <i class="bx bx-edit-alt me-1"></i> {{ trans('home.Edit') }}
                            </a>
                            <a onclick="return DeletingModal({{ $value->id }});" class="dropdown-item DeletingModal" name="{{ $value->id }}" href="javascript:void(0);">
                                <i class="bx bx-trash me-1"></i> {{ trans('home.Delete') }}
                            </a>
                            <a onclick="return exitModal({{ $value->id }});" class="dropdown-item exitModal" name="{{ $value->id }}" href="javascript:void(0);">
                                <i class="bx bx-exit me-1"></i> 
                              	خروج السياره
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
                    window.location.href = '{{ url('parking_panel/parking/destroy') }}' + '/' + ID;

                }
            });
        }
      
      
      
      	function exitModal(ID) {
            swal({
                title: "هل تريد خروج باركينج هذه السياره ؟",
                text: "بعد خروج باركينج هذه السياره لا يمكنك العودة",
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
                    window.location.href = '{{ url('parking_panel/exit_parking') }}' + '/' + ID;

                }
            });
        }

    </script>


@endsection
