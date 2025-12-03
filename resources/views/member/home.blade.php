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

  
    <link rel="stylesheet" href="{{asset('bootstrap-sweetalert')}}/sweetalert.css" />
  
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
	
	
  <a href="{{ asset('member_panel/logout') }}" class="btn btn-default" style="position:fixed;background: #EEE;right: 5%;top: 9%;">
  	تسجيل الخروج
  </a>
 
	
  <section style="padding-top:100px;margin-top:50px">
    
    <div class="container">
      	<div class="row justify-content-center">
          
          	@php
          		$user = auth()->guard('member')->user();
          		$events = App\Models\Events::where('user_id',$user->id)->get();
          	@endphp
          
          	<div class="col-md-9">
              
              <div class="row justify-content-center">

                @foreach($events as $event)
                <div class="col-md-4" style="margin-bottom:20px;cursor:pointer" onclick="location.href='{{ asset('member_panel/event-messages/'.$event->id) }}'">
                  <div class="card">
                    <img src="{{ $event->file }}" style="height: 220px;" class="card-img-top" alt="...">
                    <div class="card-body" style="padding: 10px;height: 96px;display: flex;justify-content: center;align-items: center;">
                      <h5 class="card-title" style="text-align:center;margin-bottom: 0;font-size: 16px;line-height: 1.5;">
                            {{ $event->title }}
                      </h5>
                    </div>
                  </div>
                </div>
                @endforeach

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