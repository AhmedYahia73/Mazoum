@extends('admin.layouts.master')
{{-- title --}}

@section('title','تعديل الدعوه')

@section('header')

  <style>

    .card-body .col-sm-12 , .card-body .col-sm-4 , .card-body .col-sm-6 { margin-bottom: 20px }

    .card-body { padding-bottom: 0 !important}

    .card-footer {
      padding-top: 10px !important;
      padding-left: 40px !important;
      margin-top: 0 !important;
      padding-bottom: 30px !important;
    }

    th,td { text-align: center !important;vertical-align:middle !important }

    .un_active {
        display: none
    }

    .bootstrap-select .dropdown-menu {
        transform: translate(0px, 0px) !important;
    }


    [dir=rtl] table.dataTable thead th, [dir=rtl] table.dataTable tbody td, [dir=rtl] table.dataTable tfoot th {
        padding-right: 0;
        padding-left: 0;
    }

    #event_users_table td.qr-cell {
        width: 150px !important;
        max-width: 150px !important;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

  </style>

@endsection

@section('content')



<!-- Basic Inputs start -->
<section id="basic-input">

  @include('flash-message')


  <div class="row">
    <div class="col-md-12">
      <div class="card">

        <div class="card-header">
          <h4 class="card-title">
            تعديل الدعوه
          </h4>
        </div>

        <div class="card-header" style="padding-bottom: 0;margin-top: 15px;">
            <ul class="nav nav-pills mb-3" role="tablist">

                <li class="nav-item">
                    <a href="{{ asset('admin/custom_events/'.$Item->id.'/edit') }}" class="nav-link" aria-selected="true">
                        تعديل الحدث
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ asset('admin/custom_events/'.$Item->id.'/event-visitors') }}" class="nav-link active" aria-selected="false">
                        زوار الحدث
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ asset('admin/custom_events/'.$Item->id.'/send-events') }}" class="nav-link" aria-selected="false">
                        أرسال الدعوات
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ asset('admin/custom_events/'.$Item->id.'/users') }}" class="nav-link" aria-selected="false">
                        المستخدمين
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ asset('admin/custom_events/'.$Item->id.'/event-report') }}" class="nav-link" aria-selected="false">
                        تقرير الحدث
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ asset('admin/custom_events/'.$Item->id.'/event-users') }}" class="nav-link" aria-selected="false">
                        مستخدمين الدعوه
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ asset('admin/custom_events/'.$Item->id.'/enter-event') }}" class="nav-link" aria-selected="false">
                        دخول المناسبة
                    </a>
                </li>



            </ul>
        </div>

        <div class="card-body">
            <div class="nav-align-top mb-4">

                <div class="tab-content" style="padding-top: 0;">

                    <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                        @include('admin.custom_events.add_users')
                    </div>

                </div>

            </div>
        </div>

      </div>
    </div>
  </div>
</section>
<!-- Basic Inputs end -->



@endsection





@section('footer')


    <script src="{{asset('repeater')}}/src/lib.js" type="text/javascript"></script>
    <script src="{{asset('repeater')}}/src/jquery.input.js" type="text/javascript"></script>
    <script src="{{asset('repeater')}}/src/repeater.js" type="text/javascript"></script>

    <script type="text/javascript">

        $(document).ready(function () {

            var dt_responsive_table2 = $('#event_users_table , #users_table');

            if (dt_responsive_table2.length) {
                var dt_responsive2 = dt_responsive_table2.DataTable({
                    responsive: 0,
                    paging: 0,
                    iDisplayLength: 1000,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy','excel','print'
                    ],
                    "bSort": false
                });
            }


            $('#select_all').click(function () {
               $('.check_items').prop('checked', this.checked);
            });

          	$('#select_all_v2').click(function () {
               $('.check_items').prop('checked', this.checked);
            });

            /* **************************************************************** */

            var form = $('form');

            $('.repeater-default').repeater({

                initEmpty: false,

                show: function () {

                    $(this).slideDown();

                    $('.m_selectpicker').selectpicker('refresh');
                    $(this).find('.dropup').next('button').css('display','none');


                },

                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                },

                isFirstItemUndeletable: true

            });


            /* **************************************************************** */


            $('.DeletingUser').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                        title: "Do you really want to delete this row ?",
                        text: "After deleting this delete row, you cannot go back .",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "yes",
                        cancelButtonText: "no",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            window.location.href = '{{ url('admin/custom_event_users/destroy') }}' + '/' + ID;

                        }
                    });
            });


            $('.DeletingFamily').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                        title: "Do you really want to delete this row ?",
                        text: "After deleting this delete row, you cannot go back .",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "yes",
                        cancelButtonText: "no",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            window.location.href = '{{ url('admin/custom_event_family/destroy') }}' + '/' + ID;

                        }
                    });
            });



          	$('.open_event_family').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                        title: "هل تريد تاكيد حضور الحفل",
                        text: "",
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
                            window.location.href = '{{ url('admin/open_custom_event_family') }}' + '/' + ID;

                        }
                    });
            });





        });

    </script>






@endsection
