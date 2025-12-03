@extends('assistant.layouts.master')
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
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home" aria-selected="true">
                       تعديل الدعوه
                    </button>
                </li>

                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-profile" aria-controls="navs-pills-top-profile" aria-selected="false">
                        أضافه مستخدمين
                    </button>
                </li>

                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-profile2" aria-controls="navs-pills-top-profile2" aria-selected="false">
                         مستخدمين الدعوه
                    </button>
                </li>

                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-enter-event" aria-controls="navs-pills-top-profile2" aria-selected="false">
                        دخول المناسبة
                    </button>
                </li>



            </ul>
        </div>

        <div class="card-body">
            <div class="nav-align-top mb-4">

                <div class="tab-content" style="padding-top: 0;">

                    <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                        @include('assistant.custom_events.edit_form')
                    </div>

                    <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                        @include('assistant.custom_events.add_users')
                    </div>

                    <div class="tab-pane fade" id="navs-pills-top-profile2" role="tabpanel">
                        @include('assistant.custom_events.event_users')
                    </div>

                    <div class="tab-pane fade" id="navs-pills-top-enter-event" role="tabpanel">
                        @include('assistant.custom_events.enter_event')
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

            var dt_responsive_table2 = $('#event_users_table');

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
                            window.location.href = '{{ url('assistant_panel/custom_event_users/destroy') }}' + '/' + ID;

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
                            window.location.href = '{{ url('assistant_panel/custom_event_family/destroy') }}' + '/' + ID;

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
                            window.location.href = '{{ url('assistant_panel/open_custom_event_family') }}' + '/' + ID;

                        }
                    });
            });





        });

    </script>






@endsection
