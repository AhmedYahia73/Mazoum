

<!DOCTYPE html>

@php
    $lang = 'en';
    app()->setLocale('en');
    $setting = App\Models\Setting::first();
@endphp

<html lang="en">
	<!--begin::Head-->
	<head><base href="">
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />


		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="{{asset('files/admin/css')}}/plugins.bundle.rtl.css" rel="stylesheet" type="text/css" />
		<link href="{{asset('files/admin/css')}}/prismjs.bundle.rtl.css" rel="stylesheet" type="text/css" />
		<link href="{{asset('files/admin/css')}}/style.bundle.rtl.css" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles-->


		<link href="{{asset('files/admin/css')}}/droidarabickufi.css" rel="stylesheet" type="text/css" />



		<style>

			.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-heading .menu-text,
			.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-link .menu-text,
			.menu-item.menu-item-active  i {
				color: #3699FF;
			}

			body {
				direction: ltr
			}


			.m-menu__link-badge {
				background: #FFF;
				padding: 0;
				text-align: center;
				height: 25px;
				width: 25px;
				border-radius: 50%;
				line-height: 25px;
				font-weight: bold;
			}
		</style>

		<style>

            th,td { text-align: center !important }

            div.dataTables_wrapper div.dataTables_info {
                display: none
            }

            .m-radio { display: inline-block !important }

            .m-radio>span, .m-checkbox>span { top: 6px }

            .card-body {
                 padding-top: 0
             }

             .table thead th {
                 vertical-align: bottom;
                 border-bottom: 1px solid #2ea4c2;
                 color: #1d7893;
             }

             .table-bordered {
                 border: 1px solid #2ea4c2;
             }

             .table-bordered th, .table-bordered td {
                 border: 1px solid #2ea4c2;
             }


            tr:nth-child(even) {
             background-color: #d3eef5;
            }




            @media print {


                 #print_page , .footer , header ,  #kt_header , #kt_subheader , #kt_aside , #kt_header_mobile  {
                     display: none !important
                 }

                .print_img {
                    display: block !important
                }


            }


         </style>


	</head>
	<!--end::Head-->


	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->




        <div class="d-flex flex-column flex-root">

            <!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">



				<!--begin::Wrapper-->
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">





					<!--begin::Content-->
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">


						<!--begin::Entry-->
						<div class="d-flex flex-column-fluid">
							<!--begin::Container-->
							<div class="container">
								<!--begin::Dashboard-->







<div class="col-md-12">


    @include('flash-message')

   <!--begin::Card-->
   <div id="DivIdToPrint" class="card card-custom gutter-b example example-compact">


      <div class="card-body">


        <div class="row" style="padding: 20px;padding-left: 50px;padding-right:50px">
            <div class="col-sm-12">
                <img class="print_img" src="{{ $setting->logo }}" style="height: 100px;display: block;margin: auto !important;text-align:center !important">

                <h1 style="text-align: center;margin-top: 20px;font-size: 35px;font-weight: bold;color: red;"> {{ $main_invoice['serial_number'] }} </h1>
            </div>
        </div>




            @php
                $user_inv = App\Models\Invoice_User::where('id',$main_invoice['invoice_user_id'])->first();

                if($user_inv != null) {
                    $city = App\Models\Cities::where('id',$user_inv['city_id'])->first();
                } else {
                    $city = null;
                }


            @endphp

            <div class="table-responsive" style="margin-bottom:30px">
                <div class="m-section__content">

                    <!--begin: Datatable -->
                    <table style="width: 100%" class="table table-striped- table-bordered table-hover table-checkable">
                        <thead>
                            <tr>
                                <th>
                                    {{ trans('home.name') }}
                                </th>
                                <th> {{ $user['name']  }} </th>
                                <th> {{ Carbon\Carbon::parse($user['created_at'])->format('Y-m-d') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="color: #1d7893">
                                    {{ trans('home.address') }}
                                </th>
                                <th style="color: #1d7893">
                                    @if($city != null)
                                        {{ $city->{$lang.'_name'} }}
                                    @endif

                                    {{  $user_inv != null ? ' - ' . $user_inv->address : '' }}
                                </th>
                                <th style="color: #1d7893">
                                    {{ $user['mobile'] }}
                                </th>
                            </tr>
                        </tbody>

                    </table>

                </div>
            </div>



            <div class="table-responsive" style="margin-bottom:30px">
                <div class="m-section__content" style="margin-bottom: 50px">

                    <!--begin: Datatable -->
                    <table  style="width: 100%" class="table table-striped- table-bordered table-hover table-checkable" id="m_table_1">
                        <thead>
                            <tr>
                                <th> {{ trans('home.product') }} </th>
                                <th> {{ trans('home.quantity') }}  </th>
                                <th> {{ trans('home.price') }} </th>
                                <th> {{ trans('home.total') }} </th>
                            </tr>
                        </thead>
                        <tbody>

                        @php $x = 1; @endphp
                        @foreach($details as $value)


                        <tr>

                            <td>
                                <a  style="color:blue;font-weight:bold">
                                    {{ $value->product != null ? $value->product->en_title : '' }}
                                </a>
                            </td>

                            <td>
                            <b>
                                {{ $value->quantity }}
                            </b>
                            </td>

                            <td> {{ $value->price }} {{ trans('home.aed') }} </td>

                            <td> {{ $value->total }} {{ trans('home.aed') }} </td>

                        </tr>

                        @endforeach

                        <tr>
                            <th style="font-weight: bold;font-size:15px"> {{ trans('home.sub_total') }}  </th>
                            <td colspan="5"> {{ $main_invoice['sub_total'] }} {{ trans('home.aed') }} </td>
                        </tr>


                        <tr>
                            <th style="font-weight: bold;font-size:15px"> {{ trans('home.coupon_value') }} </th>
                            <td colspan="5">
                            {{ $main_invoice->coupon != null ? $main_invoice->coupon->title : '' }}
                            ({{ $main_invoice['coupon_value'] }} ) {{ trans('home.aed') }}
                            </td>
                        </tr>

                        <tr>
                            <th style="font-weight: bold;font-size:15px">  {{ trans('home.total') }}	 </th>
                            <td colspan="5"> {{ $main_invoice['total'] }} {{ trans('home.aed') }} </td>
                        </tr>





                        </tbody>
                    </table>

                </div>
            </div>




            @if($city != null && $city->company != null)
            <div class="table-responsive" style="margin-bottom:30px">
                <div class="m-section__content">

                    <!--begin: Datatable -->
                    <table style="width: 100%" class="table table-striped- table-bordered table-hover table-checkable">
                        <thead>
                            <tr>
                                <th>
                                    this order will delivery with company {{ $city->company->name }} within {{ $city->duration }} day
                                </th>
                            </tr>
                        </thead>


                    </table>

                </div>
            </div>
            @endif





   </div>
   <!--end::Card-->




</div>









								<!--end::Dashboard-->
							</div>
							<!--end::Container-->
						</div>
						<!--end::Entry-->

					</div>
					<!--end::Content-->


					<!--begin::Footer-->
					<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
						<!--begin::Container-->
						<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
							<!--begin::Copyright-->
							<div class="text-dark order-2 order-md-1">
								<span class="text-muted font-weight-bold mr-2">{{ Carbon\Carbon::now()->format('Y') }}©</span>
								<a href="https://ebtasm.com/" target="_blank" class="text-dark-75 text-hover-primary">Web Development Ebtasm.com</a>
							</div>
							<!--end::Copyright-->

						</div>
						<!--end::Container-->
					</div>
					<!--end::Footer-->




				</div>
				<!--end::Wrapper-->

			</div>
			<!--end::Page-->

		</div>
		<!--end::Main-->










		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="{{asset('files/admin/js')}}/plugins.bundle.js"></script>

		<script src="{{asset('files/admin/js')}}/prismjs.bundle.js"></script>
		<script src="{{asset('files/admin/js')}}/scripts.bundle.js"></script>
		<!--end::Global Theme Bundle-->


	</body>
	<!--end::Body-->
</html>
