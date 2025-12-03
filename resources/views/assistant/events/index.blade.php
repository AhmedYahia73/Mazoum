@extends('assistant.layouts.master')
{{-- title --}}

@section('title','الأحداث')

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
        <div class="card-header">
          <h4 class="card-title">
            الأحداث
          </h4>
        </div>
        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> أسم الحدث </th>
                    <th> موقع الحدث </th>
                    <th> تاريخ الحدث </th>
                    <th> أسم المستخدم </th>
                    <th> صوره  </th>
                    <th> {{ trans('home.tools') }} </th>
                </tr>
              </thead>

              <tbody>

                @php $x = 1; @endphp

                @foreach($Item as $value)

                  <tr>
                      <td> {{ $x }} </td>
                      <td> {{ $value->title }} </td>

                      <td> {{ $value->address }} </td>
                      <td> {{ $value->date }} </td>
                      <td> {{ $value->user != null ? $value->user->name : '' }} </td>
                      <td> <img src="{{ $value->file }}?{{rand()}}" style="width: 130px;display: block;margin: auto;"> </td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('assistant_panel/event-details/'). '/' . $value->id }}">
                                <i class="bx bx-eye-alt me-1"></i> التفاصيل
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
                    window.location.href = '{{ url('assistant_panel/events/destroy') }}' + '/' + ID;

                }
            });
        }

    </script>


@endsection
