@extends('parking.layouts.master')
{{-- title --}}

@section('title','خروج باركينج')

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

  <div class="row">
    <div class="col-12">
      <div class="card">

        <div class="card-header" style="margin-bottom: 30px">
          <h4 class="card-title">
            خروج باركينج
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
                    <th> وقت الخروج </th>
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
                        {{ $value->out_time != null ? Carbon\Carbon::parse($value->out_time)->format('Y-m-d h:i A') : null }}
                      </td>
                      <td>
                        <a onclick="return LeavingModal({{ $value->id }});" class="dropdown-item btn btn-danger" name="{{ $value->id }}" href="javascript:void(0);">
                           تاكيد خروج
                        </a>
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

        function LeavingModal(ID) {
            swal({
                title: "",
                text: "هل تريد تاكيد خروج هذه السياره ؟",
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
                    window.location.href = '{{ url('parking_panel/confirm-leave-parking') }}' + '/' + ID;

                }
            });
        }

    </script>


@endsection
