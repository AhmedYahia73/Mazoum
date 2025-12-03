@extends('admin.layouts.master')
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

    .active_nav_item {

        background: #5a8dee !important;
    	color: #FFF !important;

    }

  </style>

@endsection



@section('content')

<div class="row" style="margin-bottom: 25px">
  <div class="col-12">

     <b>
        <label class="btn btn-warning">
            <input type="checkbox" for="checkbox_select" id="select_all" name="select_all">
            <span style="display: inline-block;padding-right:10px">
                أختر الكل
            </span>
        </label>
    </b>

    <b>
      <a href="{{asset('admin/events/create')}}" id="addRow" class="btn btn-primary">
        <i class="bx bx-plus"></i>&nbsp;
        أضافة حدث جديد
      </a>
    </b>

    <b>
        <button type="button" onclick="return deleteToSelected();" class="btn btn-danger" style="margin-left:0px;margin-bottom: 0px;">
            حذف المستخدمين
        </button>
    </b>

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
            مناسبات الكويت
          </h4>
        </div>

        <div class="card-header">

          <ul class="nav nav-tabs" style="margin-bottom:20px">
            <li class="nav-item">
              <a class="nav-link {{ request()->is('admin/events') ? 'active active_nav_item' : '' }}" aria-current="page" href="{{ asset('admin/events') }}">
              	المناسبات
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->is('admin/current-events') ? 'active active_nav_item' : '' }}" href="{{ asset('admin/current-events') }}">
              	  المناسبات الحاليه
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->is('admin/closed-events') ? 'active active_nav_item' : '' }}" href="{{ asset('admin/closed-events') }}">
              	المناسبات المنهية
              </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/deleted-events') ? 'active active_nav_item' : '' }}" href="{{ asset('admin/deleted-events') }}">
                    المناسبات المحذوفه
                </a>
              </li>
          </ul>

        </div>

        <div class="card-body card-dashboard">

            {!! Form::open(['role'=>'form','id'=>'all_events','method'=>'post','files' => true]) !!}

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
                            <th scope="row">
                                <div style="display: flex;align-items: center;justify-content: center;">
                                    <span style="font-size: 20px;"> {{ $x }} </span>
                                    <input type="checkbox" class="check_items" name="events[{{$x}}][id]" value="{{ $value->id }}" id="user{{ $value->id }}" style="cursor: pointer;margin-right: 5px;width: 20px;height: 20px;">
                                </div>
                            </th>
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
                                    <a class="dropdown-item" href="{{ url('admin/events/'). '/' . $value->id . '/edit'}}">
                                        <i class="bx bx-edit-alt me-1"></i> {{ trans('home.Edit') }}
                                    </a>
                                    <a onclick="return DeletingModal({{ $value->id }});" class="dropdown-item DeletingModal" name="{{ $value->id }}" href="javascript:void(0);">
                                        <i class="bx bx-trash me-1"></i> {{ trans('home.Delete') }}
                                    </a>
                                    @if(request()->is('admin/events'))
                                    <a onclick="return currentEventModal({{ $value->id }});" class="dropdown-item currentEventModal" name="{{ $value->id }}" href="javascript:void(0);">
                                        <i class="bx bx-check me-1"></i> المناسبات الحاليه
                                    </a>
                                    <a onclick="return closeEventModal({{ $value->id }});" class="dropdown-item closeEventModal" name="{{ $value->id }}" href="javascript:void(0);">
                                        <i class="bx bx-window-close me-1"></i> المناسبات المنهية
                                    </a>
                                    @elseif(request()->is('admin/current-events'))
                                    <a onclick="return closeEventModal({{ $value->id }});" class="dropdown-item closeEventModal" name="{{ $value->id }}" href="javascript:void(0);">
                                        <i class="bx bx-window-close me-1"></i> المناسبات المنهية
                                    </a>
                                    @else
                                    <a onclick="return unCloseEventModal({{ $value->id }});" class="dropdown-item unCloseEventModal" name="{{ $value->id }}" href="javascript:void(0);">
                                        <i class="bx bx-window-open me-1"></i> فتح الحدث
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
                </div>

            {!! Form::close() !!}

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

        $(document).ready(function () {

            $('#select_all').click(function () {
               $('.check_items').prop('checked', this.checked);
            });

        });

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
                    window.location.href = '{{ url('admin/events/destroy') }}' + '/' + ID;

                }
            });
        }


      	function currentEventModal(ID) {
            swal({
                title: "هل تريد نقل الحدث الى المناسبات الحاليه",
                text: "",
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
                    window.location.href = '{{ url('admin/current-event') }}' + '/' + ID;

                }
            });
        }


        function closeEventModal(ID) {
            swal({
                title: "هل تريد اغلاق الحدث",
                text: "",
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
                    window.location.href = '{{ url('admin/close-event') }}' + '/' + ID;

                }
            });
        }


      	function unCloseEventModal(ID) {
            swal({
                title: "هل تريد فتح الحدث",
                text: "",
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
                    window.location.href = '{{ url('admin/un-close-event') }}' + '/' + ID;

                }
            });
        }


        function deleteToSelected() {

            swal({
                title: "هل تريد حقًا حذف هذه الدعوات",
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
                  $('#all_events').attr('action', '{{ url("admin/delete_events") }}').submit();
                }
              });
        }



    </script>


@endsection
