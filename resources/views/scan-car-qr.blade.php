<!DOCTYPE html>
<html lang="en">
<head>
	<title>
		طلب خروج السياره
	</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('car/vendor') }}/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('car/fonts') }}/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('car/css') }}/util.css">
	<link rel="stylesheet" type="text/css" href="{{ asset('car/css') }}/main.css">
<!--===============================================================================================-->

    <link rel="stylesheet" href="{{asset('bootstrap-sweetalert/sweetalert.css')}}" />


    <link rel="stylesheet" href="{{ asset('arabic_font') }}/droidarabickufi.css"/>

	<style>
		body {
			direction: rtl;
			text-align: right;
		}

		 body, html, h1, h2, h3, h4, h5, h6, p, a, li, .login100-form-btn , .input100, .btn-primary , .login100-form-title {
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
        }
      
        @media(min-width:768px) {
			.wrap-login100 {
             width:460px !important
          }
        }
      
	</style>
</head>
<body>

	<div class="limiter">
		<div class="container-login100" style="display:block;background-image: url('{{ asset('car/images') }}/bg-01.jpg');">
          
          
          
          	<div class="custom" style="display: flex;justify-content: center;margin-top: 150px;">
          
              <div class="wrap-login100 p-t-30 p-b-50">

                  {!! Form::open(['url' => "post-scan-car-qr", 'role'=>'form','id'=>'scan-qr','method'=>'post', 'class' => 'login100-form  p-b-33 p-t-5']) !!}

                      <input type="hidden" name="uu_id" value="{{ $uu_id }}">

                      <span class="login100-form-title p-b-41" style="color:#000;margin-bottom: 0 !important;border-bottom: 1px solid #DDD;padding-top: 20px;padding-bottom: 20px;font-size: 22px;">
                          طلب خروج السياره
                      </span>

                      <div class="wrap-input100 validate-input" style="padding-top: 20px !important;padding-bottom: 20px !important;">
                          <input class="input100" type="text" name="serial_number" required placeholder="الكود">
                          <span class="focus-input100" data-placeholder="&#xe80f;"></span>
                      </div>

                      <div class="container-login100-form-btn m-t-32">
                          <button type="submit" form="scan-qr" class="login100-form-btn">
                              أرسال الطلب
                          </button>
                      </div>

                  {!! Form::close() !!}
              </div>
           </div>
          
           <div style="text-align: center;margin-top:15px"> <h2 style="color:#FFF;font-size:18px"> Mazoom Kwt </h2>   </div>

		</div>
	</div>



<!--===============================================================================================-->
	<script src="{{ asset('car/vendor') }}/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('car/js') }}/main.js"></script>
    <script src="{{asset('bootstrap-sweetalert/sweetalert.js')}}"></script>

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
