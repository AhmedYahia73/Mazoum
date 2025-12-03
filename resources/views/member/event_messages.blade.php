<!DOCTYPE html>
<html lang="en">
<head>
	<title> Home </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="{{ asset('member/images') }}/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('member/vendor') }}/bootstrap/css/bootstrap.min.css">

    <link rel="stylesheet" href="{{asset('bootstrap-sweetalert/sweetalert.css')}}" />

    <link rel="stylesheet" href="{{ asset('arabic_font') }}/droidarabickufi.css"/>

  	<style>
      body {
        background-image:url({{ asset('member_cover2.jpeg') }}?{{ rand() }});
        background-size: cover;
        background-attachment: fixed;
        background-repeat: no-repeat;
      }

      body, html, h1, h2, h3, h4, h5, h6, p, a, li, .m-portlet__head-text, .btn-primary , .card-text {
        font-family: 'DroidArabicKufiRegular', sans-serif !important;
      }

      body, html {
      	direction:rtl !important;
        text-align:right !important
      }
  	</style>

</head>

<body>


  <a href="{{ asset('member_panel/logout') }}" class="btn btn-default" style="position:fixed;background: #EEE;right: 10%;top: 9%;">
  	تسجيل الخروج
  </a>
  
  <a href="{{ asset('member_panel') }}" class="btn btn-default" style="position:fixed;background: #EEE;right: 5%;top: 9%;">
  	 رجوع
  </a>

  <section style="padding-top:100px;margin-top:50px">
    <div class="container">
      	<div class="row justify-content-center">

          	@php
              $mobiles = App\Models\EventUsers::where('event_id',$event->id)->pluck('mobile')->toArray();

              $mobiles_arr = [];

              foreach($mobiles as $phone) {
              	$mobiles_arr[] = ltrim($phone,"+");
              }

          		$messages = App\Models\CongratulationMessages::whereIn('mobile',$mobiles_arr)->get();
            @endphp

          	<div class="col-md-9">

              <div class="row justify-content-center">

                @if($messages != null && $messages->count() > 0)
                  @foreach($messages as $message)
                  <div class="col-md-12" style="margin-bottom:20px;background: #fbf8f8;padding-left: 0;padding-right: 0;">
                      <div class="card-header" style="color:#000">
                          {{ $message->name }}
                      </div>
                    <div class="card-body">
                      <h5 class="card-title" style="color:#000">
                          {{ $message->mobile }}
                      </h5>
                      <p class="card-text" style="color:#000">
                          {{ $message->message }}
                      </p>
                    </div>
                  </div>
                  @endforeach
                @else
                	<div class="col-md-12" style="margin-bottom:20px;">
                    <div class="card-body">
                      <p class="card-text" style="text-align: center;color: #FFF;font-size: 55px;margin-top: 150px;">
                          عفوا لا يوجد اي رسائل تهنئه
                      </p>
                    </div>
                  </div>
                @endif

              </div>

            </div>

        </div>
    </div>
  </section>


	  <div style="text-align: center;margin-top:45px"> <h2 style="color:#FFF;font-size:18px"> Mazoom Kwt </h2>   </div>

<!--===============================================================================================-->
	<script src="{{ asset('member/vendor') }}/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('member/vendor') }}/bootstrap/js/popper.js"></script>
	<script src="{{ asset('member/vendor') }}/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('member/vendor') }}/tilt/tilt.jquery.min.js"></script>

  <script src="{{asset('bootstrap-sweetalert')}}/sweetalert.min.js"></script>

  	<script>

        (function($){

            @if ($message = Session::get('success'))

                swal({
                    title: " ",
                    text: "{{ $message }}",
                    type: "success",
                });

            @endif

            @if ($message = Session::get('error'))

                swal({
                    title: "",
                    text: "{{ $message }}",
                    type: "warning",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-danger",
                });

            @endif


        })(jQuery);

    </script>


</body>
</html>
