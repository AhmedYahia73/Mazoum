@extends('admin.layouts.master')
{{-- title --}}

@section('title',trans('home.edit_user'))

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
            {{ trans('home.edit_user') }}
          </h4>
        </div>

        <div class="card-header" style="padding-bottom: 0;margin-top: 15px;">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home" aria-selected="true">
                        {{ trans('home.edit_user') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-profile" aria-controls="navs-pills-top-profile" aria-selected="false">
                        الأشتراكات
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="nav-align-top mb-4">

                <div class="tab-content" style="padding-top: 0;">

                    <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                        @include('admin.users.edit_form')
                    </div>

                    <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                        @include('admin.users.user-invoices')
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
    <script>

        $(document).ready(function() {

            $('#order_type').change(function() {

                var type = $(this).val();

                if(type == 'offer') {

                    $('.offers').removeClass('un_active');
                    $('.offers select').attr('required','required');

                    $('.users_count').addClass('un_active');
                    $('.users_count input').removeAttr('required');

                    $('.total_price').addClass('un_active');
                    $('.total_price input').removeAttr('required');

                    $('.currencies').addClass('un_active');
                    $('.currencies select').removeAttr('required');

                } else if(type == 'fixed-price') {

                    $('.users_count').removeClass('un_active');
                    $('.users_count input').attr('required','required');

                    $('.total_price').removeClass('un_active');
                    $('.total_price input').attr('required','required');

                    $('.offers').addClass('un_active');
                    $('.offers select').removeAttr('required');

                    $('.currencies').removeClass('un_active');
                    $('.currencies select').attr('required','required');

                } else {

                    $('.offers').addClass('un_active');
                    $('.offers select').removeAttr('required');

                    $('.users_count').addClass('un_active');
                    $('.users_count input').removeAttr('required');

                    $('.total_price').addClass('un_active');
                    $('.total_price input').removeAttr('required');

                    $('.currencies').addClass('un_active');
                    $('.currencies select').removeAttr('required');
                }

            });

            $('.m_selectpicker').selectpicker('refresh');

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('.m_selectpicker').selectpicker('refresh');
            });

        });

    </script>


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
                  window.location.href = '{{ url('admin/user-invoice/destroy') }}' + '/' + ID;
              }
          });
      }

    </script>

    <!-- Vendors JS -->
    <script src="{{asset('admin/assets')}}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/pickr/pickr.js"></script>

    <!-- Page JS -->
    <script>
        'use strict';

        (function () {

            // Flat Picker
            // --------------------------------------------------------------------
            const  flatpickrDateTime = document.querySelector('#flatpickr-date');

            // Datetime
            if (flatpickrDateTime) {
                flatpickrDateTime.flatpickr({
                    enableTime: false,
                    dateFormat: 'Y-m-d'
                });
            }

        })();

    </script>

@endsection



