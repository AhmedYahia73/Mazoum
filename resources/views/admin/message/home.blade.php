@extends('admin.layouts.master')
{{-- title --}}

@section('title',trans('home.messages'))

@section('header')

  <style>
    th , td {
      text-align: center !important
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
            {{ trans('home.messages') }}
          </h4>
        </div>
        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> {{ trans('home.name') }} </th>
                    <th> {{ trans('home.email') }}   </th>
                    <th> {{ trans('home.mobile') }}   </th>
                    <th> {{ trans('home.message') }}  </th>
                    <th> {{ trans('home.tools') }} </th>
                </tr>
              </thead>

              <tbody>

                @php $x = 1; @endphp

                @foreach($Item as $value)

                    <tr>
                        <td> {{ $x }} </td>
                        <td>{{ $value->first_name }} {{ $value->last_name }}</td>
                        <td>{{ $value->email  }}</td>
                        <td>{{ $value->mobile  }}</td>
                        <td>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{$value->id}}">
                                الرسالة
                            </button>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                <a onclick="return DeletingModal({{ $value->id }});" class="dropdown-item DeletingModal" name="{{ $value->id }}" href="javascript:void(0);">
                                    <i class="bx bx-trash me-1"></i> {{ trans('home.Delete') }}
                                </a>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal{{$value->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    {{ trans('home.message') }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    {{$value->message}}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    {{ trans('home.close') }}
                                </button>
                            </div>
                            </div>
                        </div>
                    </div>

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
                    window.location.href = '{{ url('admin/messages/destroy') }}' + '/' + ID;

                }
            });
        }

    </script>


@endsection
