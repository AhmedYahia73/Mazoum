<?php $__env->startSection('title',@$Item->user->name); ?>

<?php $__env->startSection('header'); ?>

  <style>

    .card-body .col-sm-12 , .card-body .col-sm-4 , .card-body .col-sm-6 { margin-bottom: 20px }

    .card-body { padding-bottom: 0 !important}

    .card-footer {
      padding-top: 10px !important;
      padding-left: 40px !important;
      margin-top: 0 !important;
      padding-bottom: 30px !important;
    }

    .bootstrap-select .dropdown-menu {
        transform: translate(0px, 0px) !important;
    }


    div.dt-buttons {
        margin-bottom: 30px !important
    }

    .table-bordered > :not(caption) > * {
        border-width: 0;
    }

    .nav-align-top .table:not(.table-dark) td {
    	border-color: transparent;
    }

  </style>

	<style>
        #map {
            width: 100%;
            height: 400px;
        }
        .controls {
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }
        #searchInput {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 0px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 100%;
            margin-bottom: 1px;
            height: 45px
        }
        #searchInput:focus {
            border-color: #4d90fe;
        }
        .geo-data{
            border: 2px dashed #2A99CF;
            padding: 10px;
            font-size: 17px;
            list-style: none;
            text-align: right;
        }
        .geo-data span{
            color: #ed691f;
        }

        .gm-style .gm-style-iw-c,
        .gm-style .gm-style-iw-t::after {
            display: none;
        }


      	[dir=rtl] table.dataTable thead .sorting::before, [dir=rtl] table.dataTable thead .sorting_asc::before,
        [dir=rtl] table.dataTable thead .sorting_desc::before, [dir=rtl] table.dataTable thead .sorting_asc_disabled::before,
        [dir=rtl] table.dataTable thead .sorting_desc_disabled::before ,
      	[dir=rtl] table.dataTable thead .sorting::after, [dir=rtl] table.dataTable thead .sorting_asc::after,
        [dir=rtl] table.dataTable thead .sorting_desc::after, [dir=rtl] table.dataTable thead .sorting_asc_disabled::after,
        [dir=rtl] table.dataTable thead .sorting_desc_disabled::after {
      		display:none
      	}

      	[dir=rtl] table.dataTable thead th, [dir=rtl] table.dataTable tbody td, [dir=rtl] table.dataTable tfoot th {
            padding-right: 1.5rem;
            padding-left: 5px;
            padding-right: 5px !important;
        }

        .hasTime .flatpickr-innerContainer {
            display: none !important
        }

        .hasTime .flatpickr-months {
            display: none !important
        }

        .light-style .flatpickr-calendar.hasTime .flatpickr-time {
            direction: ltr;
        }

        .card .nav-link {
            padding: 6px 14px;
            font-size: 15px;
        }

        .bootstrap-select .dropdown-toggle {
            background: transparent !important;
            box-shadow: none !important;
        }

    </style>


  <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/flatpickr/flatpickr.css" />
  <link rel="stylesheet" href="<?php echo e(asset('admin/assets')); ?>/vendor/libs/pickr/pickr-themes.css" />


<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>

    <!-- Cards with badge -->
    <div class="row justify-content-center" style="margin-top: 20px">

        <div class="col-12">
            <?php echo $__env->make('flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <div class="col-12">
            <div class="card">


                <div class="card-header" style="padding-bottom: 0;margin-top: 15px;">
                    <ul class="nav nav-pills mb-3" role="tablist">

                        <li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/edit')); ?>" class="nav-link " aria-selected="false">
                                تعديل
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/event-visitors')); ?>" class="nav-link" aria-selected="false">
                                المدعوين
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/send-events')); ?>" class="nav-link" aria-selected="false">
                                أرسال الدعوات
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/event-report')); ?>" class="nav-link" aria-selected="false">
                                تفاصيل المناسبة
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/event-users')); ?>" class="nav-link" aria-selected="false">
                                  دخول المناسبة
                            </a>
                        </li>

                      	<li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/event-location')); ?>" class="nav-link active" aria-selected="true">
                                موقع الحدث
                            </a>
                        </li>


                      	<li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/enter-event')); ?>" class="nav-link" aria-selected="false">
                                  مسموح لهم بالدخول
                            </a>
                        </li>

                      	<li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/scanner')); ?>" class="nav-link" aria-selected="false">
                                 Scaner
                            </a>
                        </li>

                      	<li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/my-package')); ?>" class="nav-link" aria-selected="false">
     							باقتي
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo e(asset('admin/events/'.$Item->id.'/chat-list')); ?>" class="nav-link" aria-selected="false">
                                 المحادثات
                            </a>
                        </li>


                    </ul>
                </div>

                <div class="card-body">
                    <div class="nav-align-top mb-4">

                        <div class="tab-content" style="padding-top: 0;">

                            <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel" style="padding-top: 30px">
                                <?php echo $__env->make('admin.events.event_location', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>


                        </div>

                    </div>
                </div>


            </div>
        </div>

    </div>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('footer'); ?>

    <script>
        // is number //
        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>

    <!-- Vendors JS -->
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('admin/assets')); ?>/vendor/libs/pickr/pickr.js"></script>

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

    <script src="<?php echo e(asset('repeater')); ?>/src/lib.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('repeater')); ?>/src/jquery.input.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('repeater')); ?>/src/repeater.js" type="text/javascript"></script>

    <script type="text/javascript">

        $(document).ready(function () {

            $('#select_all').click(function () {
               $('.check_items').prop('checked', this.checked);
            });

          	$('#select_all_v2').click(function () {
               $('.check_items').prop('checked', this.checked);
            });

            /* **************************************************************** */

            $('.new_send_btn').click(function() {

                swal({
                        title: "هل تريد تاكيد أرسال الدعوات لهولاء المستخدمين ",
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
                            $('#send_event_users').attr('action','<?php echo e(asset('admin/new-send-event-invitation')); ?>').attr('method','post').submit();
                        }
                    }
                );
            });



          	$('#delete_selected_items_btn,.delete_selected_items_btn').click(function() {

                swal({
                        title: "هل تريد حذف الصفوف المختارة ",
                        text: "بعد الحذف لا يمكنك العودة.",
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
                            $('#send_event_users').attr('action','<?php echo e(asset('admin/delete_selected_event_users')); ?>').attr('method','post').submit();
                        }
                    }
                );
            });


          	$('#delete_selected_items_btn_v2,.delete_selected_items_btn_v2').click(function() {

                swal({
                        title: "هل تريد حذف الصفوف المختارة ",
                        text: "بعد الحذف لا يمكنك العودة.",
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
                            $('#send_event_users_v2').attr('action','<?php echo e(asset('admin/delete_selected_event_users')); ?>').attr('method','GET').submit();
                        }
                    }
                );
            });

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
                            window.location.href = '<?php echo e(url('admin/event_users/destroy')); ?>' + '/' + ID;

                        }
                    });
            });


          	/* **************************************************************** */


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
                            window.location.href = '<?php echo e(url('admin/event_family/destroy')); ?>' + '/' + ID;

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
                            window.location.href = '<?php echo e(url('admin/open_event_family')); ?>' + '/' + ID;

                        }
                    });
            });





	       /* **************************************************************** */

          $('.login_event').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                        title: "هل تريد حقا تاكيد دخول هذا المستخدم",
                        text: "",
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
                            window.location.href = '<?php echo e(url('admin/login-user')); ?>' + '/' + ID;

                        }
                    });
            });


          	$('.send_qr_code').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                  title: "هل تريد أرسال QR ؟",
                  text: "",
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
                    window.location.href = '<?php echo e(url('admin/send-qr')); ?>' + '/' + ID;

                  }
                });
            });


          	$('.send_new_qr_code').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                  title: "هل تريد أرسال QR ؟ بعد التحديث مره اخري",
                  text: "",
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
                    window.location.href = '<?php echo e(url('admin/send-new-qr')); ?>' + '/' + ID;

                  }
                });
            });



          	$('.accept_event').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                  title: "هل تريد قبول الدعوه",
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
                    window.location.href = '<?php echo e(url('admin/accept-user-event')); ?>' + '/' + ID;

                  }
                });
            });


          $('.qr_is_send').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                  title: "هل تريد تاكيد ارسال ال qr",
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
                    window.location.href = '<?php echo e(url('admin/qr-is-send')); ?>' + '/' + ID;

                  }
                });
            });


          $('.is_send_event').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                  title: "هل تريد تاكيد ارسال الدعوه",
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
                    window.location.href = '<?php echo e(url('admin/is-send-event')); ?>' + '/' + ID;

                  }
                });
            });


          $('.refuse_event').on('click', function () {

                var ID = $(this).attr("name");

                swal({
                  title: "هل تريد رفض الدعوه",
                  text: "",
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
                    window.location.href = '<?php echo e(url('admin/refuse-user-event')); ?>' + '/' + ID;

                  }
                });
            });



        });


    </script>

    <script>
        'use strict';
        $(function () {

            var dt_responsive_table = $('#no_updates_table');

            if (dt_responsive_table.length) {
                var dt_responsive = dt_responsive_table.DataTable({
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


          	var all_users_table = $('#send_events_table');

            if (all_users_table.length) {
                var dt_responsive4 = all_users_table.DataTable({
                    responsive: 0,
                    paging: 0,
                    iDisplayLength: 1000,
                    dom: 'Bfrtip',
                    buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column
                        }
                    }
                ],
                    "bSort": false
                });
            }


          	$('.SendCongratulationMessageToSelected').click(function() {

                var ID = $(this).attr('data-id');

                var formDev = $('#SendCongratulationMessage'+ID);
                var msg1 = formDev.find('.msg1').val();

              	$('.msg1').val(msg1);

                $('#send_event_users').attr('action', '<?php echo e(url("admin/send-congratulation-message")); ?>').submit();


            });


          	$('.SendApologizeMessageToSelected').click(function() {

                var ID = $(this).attr('data-id');

                var formDev = $('#SendApologizeMessage'+ID);
                var msg2 = formDev.find('.msg2').val();

              	$('.msg2').val(msg2);

                $('#send_event_users').attr('action', '<?php echo e(url("admin/send-apologize-message")); ?>').submit();


            });


        });
    </script>


	<script>

        function SendCongratulations(ID) {
            swal({
                title: "",
                text: "هل تريد أرسال تهنئه للكل ؟",
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
                    window.location.href = '<?php echo e(url('admin/send-congratulations')); ?>' + '/' + ID;

                }
            });
        }

    </script>



	<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(env('API_GOOGLE_MAP')); ?>&libraries=places&callback=initMap" async defer></script>

    <script>
        function initMap() {

            var initialLat = '<?php echo e($Item->lat != null ? $Item->lat : '29.378586'); ?>';
            var initialLong = '<?php echo e($Item->long != null ? $Item->long : '47.990341'); ?>';
            initialLat = initialLat?initialLat:55.270208;
            initialLong = initialLong?initialLong:-115.141000;

            var latlng = new google.maps.LatLng(initialLat, initialLong);

            var options = {
                zoom: 13,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            var map = new google.maps.Map(document.getElementById('map'), options);


            var input = document.getElementById('searchInput');
            //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);


            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            var infowindow = new google.maps.InfoWindow();

            var marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: latlng
            });

            /*
                document.getElementById('lat').innerHTML = marker.getPosition().lat();
                document.getElementById('lon').innerHTML = marker.getPosition().lng();
            */

            document.getElementById('lat').innerHTML = <?php if($Item->lat != null): ?> <?php echo e($Item->lat); ?> <?php else: ?> '' <?php endif; ?>;
            document.getElementById('lon').innerHTML = <?php if($Item->long != null): ?> <?php echo e($Item->long); ?> <?php else: ?> '' <?php endif; ?>;

            document.getElementById('lat2').value = marker.getPosition().lat();
            document.getElementById('lon2').value = marker.getPosition().lng();

            document.getElementById('country2').value = '<?php echo e($Item->country != null ? $Item->country : 'United Arab Emirates'); ?>';
            document.getElementById('location2').value = '<?php echo e($Item->location != null ? $Item->location : 'Dubai - United Arab Emirate'); ?>';

            document.getElementById('country').innerHTML = '<?php echo e($Item->country); ?>';
            document.getElementById('location').innerHTML = '<?php echo e($Item->location); ?>';


            autocomplete.addListener('place_changed', function() {

                //infowindow.close();
                //marker.setVisible(false);

                var place = autocomplete.getPlace();

                if (!place.geometry) {
                    window.alert("المكان الذي تم إرجاعه من خلال البحث التلقائي لا يحتوي على هندسة");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                    map.setZoom(3);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                map.setCenter(place.geometry.location);


                /*
                marker.setIcon(({
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(35, 35)
                }));
                */



                marker.setPosition(place.geometry.location);
                marker.setVisible(true);


                var address = '';
                if (place.address_components) {
                    address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                    ].join(' ');
                }

                //infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                infowindow.open(map, marker);

                // Location details


                for (var i = 0; i < place.address_components.length; i++) {
                    if(place.address_components[i].types[0] == 'country'){
                        document.getElementById('country').innerHTML = place.address_components[i].long_name;
                        document.getElementById('country2').value = place.address_components[i].long_name;
                    }
                }

                document.getElementById('location').innerHTML = place.formatted_address;
                document.getElementById('location2').value = place.formatted_address;

                document.getElementById('lat').innerHTML = place.geometry.location.lat();
                document.getElementById('lon').innerHTML = place.geometry.location.lng();


                document.getElementById('lat2').value = place.geometry.location.lat();
                document.getElementById('lon2').value = place.geometry.location.lng();


            });



            map2 = new google.maps.Map(document.getElementById("map"), options);

            geocoder = new google.maps.Geocoder();

            marker = new google.maps.Marker({
                map: map2,
                draggable: true,
                position: latlng
            });

            google.maps.event.addListener(marker, "dragend", function () {

                var point = marker.getPosition();

                map.panTo(point);

                geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {

                        map.setCenter(results[0].geometry.location);
                        marker.setPosition(results[0].geometry.location);

                        /*
                        $('.search_addr').val(results[0].formatted_address);
                        $('.search_latitude').val(marker.getPosition().lat());
                        $('.search_longitude').val(marker.getPosition().lng());
                        */

                        var full = results[0].address_components;

                        document.getElementById('location').innerHTML = results[0].formatted_address;
                        document.getElementById('country').innerHTML = JSON.stringify(full[3].long_name);

                        document.getElementById('location2').value = results[0].formatted_address;
                        document.getElementById('country2').value = JSON.stringify(full[3].long_name);

                        document.getElementById('lat').innerHTML = marker.getPosition().lat();
                        document.getElementById('lon').innerHTML = marker.getPosition().lng();

                        document.getElementById('lat2').value = marker.getPosition().lat();
                        document.getElementById('lon2').value = marker.getPosition().lng();



                    }
                });

            });

        }
    </script>


    <script>

        function sendMessageToSelected() {
            swal({
                title: "هل تريد حقًا ارسال رسالة",
                text: "بعد الأرسال ، لا يمكنك العودة.",
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
                  $('#send_event_users').attr('action', '<?php echo e(url("admin/send-custom-message")); ?>').submit();
                }
              });
        }



		function SendCongratulationMessageToSelected() {
            swal({
                title: "هل تريد حقًا ارسال رسالة",
                text: "بعد الأرسال ، لا يمكنك العودة.",
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
                  $('#send_event_users').attr('action', '<?php echo e(url("admin/send-congratulation-message")); ?>').submit();
                }
              });
        }


      	function SendApologizeMessageToSelected() {
            swal({
                title: "هل تريد حقًا ارسال رسالة",
                text: "بعد الأرسال ، لا يمكنك العودة.",
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
                  $('#send_event_users').attr('action', '<?php echo e(url("admin/send-apologize-message")); ?>').submit();
                }
              });
        }

    </script>

	<script src="<?php echo e(asset('qr_code_program/html5-qrcode.min.js')); ?>"></script>

    <script>
        function docReady(fn) {
            // see if DOM is already available
            if (document.readyState === "complete"
                || document.readyState === "interactive") {
                // call on next available tick
                setTimeout(fn, 1);
            } else {
                document.addEventListener("DOMContentLoaded", fn);
            }
        }

        docReady(function () {
            var resultContainer = document.getElementById('qr-reader-results');
            var lastResult, countResults = 0;
            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText !== lastResult) {
                    ++countResults;
                    lastResult = decodedText;
                    // Handle on success condition with the decoded message.
                    console.log(`Scan result ${decodedText}`, decodedResult);
                    // alert(`Scan result ${decodedText}`, decodedResult);
                    // window.location.href = decodedText;
                    $('<a href="'+decodedText+'" target="blank"></a>')[0].click();
                    //window.open(decodedText, '_blank');

                }
            }

            var html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", { fps: 10, qrbox: 250 });
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/admin/event_location/index.blade.php ENDPATH**/ ?>