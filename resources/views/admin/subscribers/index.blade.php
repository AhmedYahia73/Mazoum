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
                @if(count($Item))
                <table class="table zero-configuration">

                        <thead class=" text-primary">
                            <th> ID </th>
                            <th> email   </th>
                            <th class="text-right"> Control </th>
                        </thead>

                        @php $x = 1; @endphp

                        <tbody>

                            @foreach ($Item as $value)

                                @php
                                    if($value->seen == 0) {
                                        $color = 'red';
                                    } else {
                                        $color = 'green';
                                    }
                                @endphp

                                <tr>
                                    <td>
                                        <div>
                                            <span> {{ $x }}  </span>
                                            <span style="width: 10px;height:10px;border-radius: 50%;margin-right: 10px;background: {{ $color }};display: inline-block;"> </span>
                                        </div>
                                    </td>
                                    <td>{{ $value->email  }}</td>

                                    <td class="td-actions text-right">
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button onclick="return DeletingModal({{ $value->id }});" class="DeletingModal dropdown-item show_confirm" type="button"  name="{{ $value->id }}" data-toggle="tooltip" title='Delete'>
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                                @if($value->seen == 0)
                                                <a href="{{ asset('admin/subscribers/seen/'.$value->id) }}" class="dropdown-item" name="{{ $value->id }}" href="javascript:void(0);">
                                                    <i class="fa fa-eye me-1"></i> read
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>


                                @php $x = $x + 1; @endphp

                            @endforeach


                        </tbody>

                    </table>
                @else
                    <h3 class="text-center">No Messages Found</h3>
                @endif

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
                    window.location.href = '{{ url('admin/subscribers/destroy') }}' + '/' + ID;

                }
            });
        }

    </script>


@endsection
