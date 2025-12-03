@extends('admin.layouts.master')
{{-- title --}}

@section('title','الموظفين')


@section('content')


<div class="row">
  <div class="col-12">
    <p>
      <a href="{{asset('admin/assistant/create')}}" id="addRow" class="btn btn-primary">
        <i class="bx bx-plus"></i>&nbsp;
        أضافة موظف جديد
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

        <div class="card-header">
          <h4 class="card-title">
            الموظفين
          </h4>
        </div>

        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> {{ trans('home.name') }}  </th>
                    <th> {{ trans('home.email') }} </th>
                    <th> {{ trans('home.mobile') }} </th>
                    <th> {{ trans('home.tools') }} </th>
                </tr>
              </thead>

              <tbody>

                @php $x = 1; @endphp

                @foreach($Item as $value)

                  <tr>
                      <td> {{ $x }} </td>
                      <td> {{ $value->name }} </td>
                      <td> {{ $value->email }} </td>
                      <td> {{ $value->mobile }} </td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('admin/assistant/'). '/' . $value->id . '/edit'}}">
                                <i class="bx bx-edit-alt me-1"></i> {{ trans('home.Edit') }}
                            </a>
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
</section>
<!--/ Zero configuration table -->

@endsection


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
                    window.location.href = '{{ url('admin/main_categories/destroy') }}' + '/' + ID;

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
                        window.location.href = '{{ url('admin/assistant/destroy') }}' + '/' + ID;

                    }
                });
            });

        });

    </script>

@endsection
