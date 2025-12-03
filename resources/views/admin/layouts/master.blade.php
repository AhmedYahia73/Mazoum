<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed"
  dir="rtl"
  data-theme="theme-default"
  data-assets-path="{{asset('admin/assets')}}/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title> @yield('title') </title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('logo.ico')}}" />


    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{asset('admin/assets')}}/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{asset('admin/assets')}}/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{asset('admin/assets')}}/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset('admin/assets')}}/js/config.js"></script>

    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/bootstrap-select/bootstrap-select.css" />

    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/select2/select2.css" />

    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/flatpickr/flatpickr.css" />

    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{asset('admin/assets')}}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{asset('custom_datables')}}/css/buttons.dataTables.min.css" />

    <link rel="stylesheet" href="{{ asset('arabic_font') }}/droidarabickufi.css"/>

    <link rel="stylesheet" href="{{asset('bootstrap-sweetalert/sweetalert.css')}}" />

    <style>

      .table:not(.table-dark) thead:not(.table-dark) th {
          letter-spacing: 0;
      }

        body, html, h1, h2, h3, h4, h5, h6, p, a, li, .m-portlet__head-text, .btn-primary {
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
        }

        .select2-container {
            z-index: 1 !important;
        }

        #template-customizer {
            display: none !important;
        }

        .sweet-alert h2 {
            color: #777;
        }

        .sweet-alert .cancel {
            background: red;
            color: #fff;
        }

        .card-footer {
            padding-top: 5px !important;
        }

        .card-title {
            margin-bottom: 0 !important;
        }

        .card-header:first-child {
            padding-bottom: 0 !important;
        }

        .ticket_table td {
            border-bottom: 0 !important;
            padding: 6px 0px !important;
        }

        .select2-container {
            z-index: 1 !important;
        }

        #template-customizer {
            display: none
        }

        /*
        body { background: #f3f4f4 }
        */
    </style>

    <style>
        body {
            overflow-x: hidden !important;
        }

        .buttons-html5 , .buttons-print {
            color: #fff !important;
            background-color: #5a8dee !important;
            border-color: #5a8dee !important;
            box-shadow: 0 0.125rem 0.25rem rgb(147 158 170 / 40%) !important;
        }
      
        @media (min-width: 1400px) {
            .container-xxl, .container-xl, .container-lg, .container-md, .container-sm, .container {
                max-width: -webkit-fill-available;
            }
        }
    </style>

    @yield('header')

    @yield('header_v2')

  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="{{ asset('admin') }}" class="app-brand-link">
              <span class="app-brand-logo demo" style="width: auto;height: auto;margin-top: 5px;margin-bottom: 5px;">
              <img src="{{ asset('logo.png') }}" style="height: 50px">
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
              <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-divider mt-0"></div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            @include('admin.layouts.sidebar')
          </ul>


        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="container-fluid">
              <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                  <i class="bx bx-menu bx-sm"></i>
                </a>
              </div>

              <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                <ul class="navbar-nav flex-row align-items-center ms-auto">

                    <!-- Style Switcher -->
                    <li class="nav-item me-2 me-xl-0">
                      <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                        <i class="bx bx-sm"></i>
                      </a>
                    </li>
                    <!--/ Style Switcher -->


                    <!-- Notification -->
                    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                      <a
                        class="nav-link dropdown-toggle hide-arrow"
                        href="javascript:void(0);"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        aria-expanded="false"
                      >
                        <i class="bx bx-bell bx-sm"></i>
                        <span class="badge bg-danger rounded-pill badge-notifications">5</span>
                      </a>
                      <ul class="dropdown-menu dropdown-menu-end py-0">
                        <li class="dropdown-menu-header border-bottom">
                          <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">Notification</h5>
                            <a
                              href="javascript:void(0)"
                              class="dropdown-notifications-all text-body"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              title="Mark all as read"
                              ><i class="bx fs-4 bx-envelope-open"></i
                            ></a>
                          </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container">
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <img src="{{asset('admin/assets')}}/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">Congratulation Lettie 🎉</h6>
                                  <p class="mb-0">Won the monthly best seller gold badge</p>
                                  <small class="text-muted">1h ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">Charles Franklin</h6>
                                  <p class="mb-0">Accepted your connection</p>
                                  <small class="text-muted">12hr ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <img src="{{asset('admin/assets')}}/img/avatars/2.png" alt class="w-px-40 h-auto rounded-circle" />
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">New Message ✉️</h6>
                                  <p class="mb-0">You have new message from Natalie</p>
                                  <small class="text-muted">1h ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-success"
                                      ><i class="bx bx-cart"></i
                                    ></span>
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">Whoo! You have new order 🛒</h6>
                                  <p class="mb-0">ACME Inc. made new order $1,154</p>
                                  <small class="text-muted">1 day ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <img src="{{asset('admin/assets')}}/img/avatars/9.png" alt class="w-px-40 h-auto rounded-circle" />
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">Application has been approved 🚀</h6>
                                  <p class="mb-0">Your ABC project application has been approved.</p>
                                  <small class="text-muted">2 days ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-success"
                                      ><i class="bx bx-pie-chart-alt"></i
                                    ></span>
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">Monthly report is generated</h6>
                                  <p class="mb-0">July monthly financial report is generated</p>
                                  <small class="text-muted">3 days ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <img src="{{asset('admin/assets')}}/img/avatars/5.png" alt class="w-px-40 h-auto rounded-circle" />
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">Send connection request</h6>
                                  <p class="mb-0">Peter sent you connection request</p>
                                  <small class="text-muted">4 days ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <img src="{{asset('admin/assets')}}/img/avatars/6.png" alt class="w-px-40 h-auto rounded-circle" />
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">New message from Jane</h6>
                                  <p class="mb-0">Your have new message from Jane</p>
                                  <small class="text-muted">5 days ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                              <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                  <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-warning"
                                      ><i class="bx bx-error"></i
                                    ></span>
                                  </div>
                                </div>
                                <div class="flex-grow-1">
                                  <h6 class="mb-1">CPU is running high</h6>
                                  <p class="mb-0">CPU Utilization Percent is currently at 88.63%,</p>
                                  <small class="text-muted">5 days ago</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                    ><span class="badge badge-dot"></span
                                  ></a>
                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                    ><span class="bx bx-x"></span
                                  ></a>
                                </div>
                              </div>
                            </li>
                          </ul>
                        </li>
                        <li class="dropdown-menu-footer border-top">
                          <a href="javascript:void(0);" class="dropdown-item d-flex justify-content-center p-3">
                            View all notifications
                          </a>
                        </li>
                      </ul>
                    </li>
                    <!--/ Notification -->


                    @php
                        $admin = Auth::guard('admin')->user();
                    @endphp

                  <!-- User -->
                  <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                      <div class="avatar avatar-online">
                        <img src="{{asset('admin/assets')}}/img/avatars/1.png" alt class="rounded-circle" />
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <a class="dropdown-item" href="pages-account-settings-account.html">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar avatar-online">
                                <img src="{{asset('admin/assets')}}/img/avatars/1.png" alt class="rounded-circle" />
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <span class="fw-semibold d-block lh-1">{{$admin->name}}</span>
                              <small>
                                admin
                              </small>
                            </div>
                          </div>
                        </a>
                      </li>

                      <li>
                        <div class="dropdown-divider"></div>
                      </li>
                      <li>
                        <a class="dropdown-item" href="{{asset('admin/logout')}}">
                          <i class="bx bx-power-off me-2"></i>
                          <span class="align-middle">Logout</span>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <!--/ User -->
                </ul>
              </div>

            </div>
          </nav>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">

              @yield('content')

            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme"  style="display:none">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div style="width: 100%;margin-bottom:10px">
                  <p class="clearfix mb-0" style="text-align: center">
                    <span class="float-left d-inline-block"><script>document.write(new Date().getFullYear())</script> &copy; </span>
                    <a href="https://acacia-uae.ae/" target="_blank" class="text-dark-75 text-hover-primary"> acacia-uae.ae </a>
                  </p>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{asset('admin/assets')}}/vendor/libs/jquery/jquery.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/popper/popper.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/js/bootstrap.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="{{asset('admin/assets')}}/vendor/libs/hammer/hammer.js"></script>

    <script src="{{asset('admin/assets')}}/vendor/libs/i18n/i18n.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="{{asset('admin/assets')}}/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Main JS -->
    <script src="{{asset('admin/assets')}}/js/main.js"></script>

    <script src="{{asset('admin/assets')}}/vendor/libs/jquery-repeater/jquery-repeater.js"></script>

    <!-- Vendors JS -->
    <script src="{{asset('admin/assets')}}/vendor/libs/datatables/jquery.dataTables.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>

    <script src="{{asset('custom_datables')}}/js/dataTables.buttons.min.js"></script>
    <script src="{{asset('custom_datables')}}/js/jszip.min.js"></script>
    <script src="{{asset('custom_datables')}}/js/pdfmake.min.js"></script>
    <script src="{{asset('custom_datables')}}/js/vfs_fonts.js"></script>
    <script src="{{asset('custom_datables')}}/js/buttons.html5.min.js"></script>
    <script src="{{asset('custom_datables')}}/js/buttons.print.min.js"></script>
    <script src="{{asset('custom_datables')}}/js/dataTables.fixedColumns.min.js"></script>
    <script src="{{asset('custom_datables')}}/js/dataTables.responsive.min.js"></script>

    <script src="{{asset('admin/assets')}}/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="{{asset('admin/assets')}}/vendor/libs/flatpickr/flatpickr.js"><script>
    <script src="{{asset('admin/assets')}}/vendor/libs/select2/select2.js"></script>
    <script src="{{asset('bootstrap-sweetalert/sweetalert.js')}}"></script>

    <script>
        'use strict';

        $(function () {

            $('#formFile').change(function(){
                const file = this.files[0];
                console.log(file);
                if (file){
                  let reader = new FileReader();
                  reader.onload = function(event){
                    console.log(event.target.result);
                    $('#imgPreview').attr('src', event.target.result);
                  }
                  reader.readAsDataURL(file);
                }
            });

            $('#upload').change(function(){
                const file = this.files[0];
                console.log(file);
                if (file){
                  let reader = new FileReader();
                  reader.onload = function(event){
                    console.log(event.target.result);
                    $('#uploadedAvatar').attr('src', event.target.result);
                  }
                  reader.readAsDataURL(file);
                }
            });


            @if(! isset($orders))
                var dt_responsive_table = $('.zero-configuration');

                if (dt_responsive_table.length) {
                    var dt_responsive = dt_responsive_table.DataTable({
                        responsive: !0,
                        paging: !0,
                      	"aLengthMenu": [[100, 200, 300, 400, 500, 600, 700, 800, 900, 1000 , -1], [100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, "All"]],
						"iDisplayLength" : 1000,
                        columnDefs: [
                            {
                                targets: -1,
                                orderable: !1
                            }
                        ]
                    });
                }
            @endif

            /* ********************************************************** */

            const selectPicker = $('.selectpicker');

            // Bootstrap Select
            // --------------------------------------------------------------------
            if (selectPicker.length) {
                selectPicker.selectpicker();
            }

            const flatpickrDate1 = document.querySelector('#flatpickr-date1');
            const flatpickrDate2 = document.querySelector('#flatpickr-date2');
            const flatpickrDate3 = document.querySelector('#flatpickr-date3');

            const flatpickrDates = document.querySelectorAll('.flatpickr-date');

            flatpickrDates.forEach(function (element) {
                flatpickr(element, {
                    monthSelectorType: 'static',
                });
            });


            // flatpickr Date 1
            if (flatpickrDate1) {
                flatpickrDate1.flatpickr({
                    monthSelectorType: 'static',
                });
            }

            // flatpickr Date 2
            if (flatpickrDate2) {
                flatpickrDate2.flatpickr({
                monthSelectorType: 'static'
                });
            }

            // flatpickr Date 1
            if (flatpickrDate3) {
                flatpickrDate3.flatpickr({
                    enableTime: true,
                    monthSelectorType: 'static',
                    dateFormat: "H:i",
                });
            }


            // Select2
            // --------------------------------------------------------------------

            const select2 = $('.select2');

            // Default
            if (select2.length) {
                select2.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        //placeholder: 'Select value',
                        //dropdownParent: $this.parent()
                    });
                });
            }


        });

    </script>

    <script>
        (function($){

            $('#eye1').click(function () {
                $('#password1').attr('type', $('#password1').is(':password') ? 'text' : 'password');
                if ($('#password1').attr('type') === 'password') {
                    $('#eye1 i').removeClass('bx-show').addClass('bx-hide');
                } else {
                    $('#eye1 i').removeClass('bx-hide').addClass('bx-show');
                }
            });

            $('#eye2').click(function () {
                $('#password2').attr('type', $('#password2').is(':password') ? 'text' : 'password');
                if ($('#password2').attr('type') === 'password') {
                    $('#eye2 i').removeClass('bx-show').addClass('bx-hide');
                } else {
                    $('#eye2 i').removeClass('bx-hide').addClass('bx-show');
                }
            });

            $('.m_selectpicker').selectpicker();

        })(jQuery);
    </script>

    @yield('footer')

    @yield('footer_v2')


  </body>
</html>
