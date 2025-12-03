@extends('admin.layouts.master')
{{-- title --}}

@section('title','التصميمات')

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
      <a href="{{asset('admin/desgins/create')}}" id="addRow" class="btn btn-primary">
        <i class="bx bx-plus"></i>&nbsp;
        أضافة تصميم جديد
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
            التصميمات
          </h4>
        </div>
        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> أسم التصميم </th>
                    <th> صوره / pdf </th>
                    <th> {{ trans('home.tools') }} </th>
                </tr>
              </thead>

              <tbody>

                @php $x = 1; @endphp

                @foreach($Item as $value)

                  <tr>
                      <td> {{ $x }} </td>
                      <td>
                        {{ $value->ar_name }}
                      </td>

                      @if($value->type == 'image')
                      <td>
                        <img src="{{ $value->file }}?{{rand()}}" style="width: 130px;display: block;margin: auto;">
                      </td>
                      @else
                      <td>
                        <a href="{{ asset('admin/desgins/show-pdf/'.$value->id) }}" target="_blank">
                            Open PDF
                        </a>
                      </td>
                      @endif
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('admin/desgins/'). '/' . $value->id . '/edit'}}">
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
                    window.location.href = '{{ url('admin/desgins/destroy') }}' + '/' + ID;

                }
            });
        }

    </script>


@endsection
