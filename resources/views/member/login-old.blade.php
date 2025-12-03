<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="{{ asset('member/images') }}/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('member/vendor') }}/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('member/fonts') }}/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('member/vendor') }}/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{ asset('member/vendor') }}/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('member/vendor') }}/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('member/css') }}/util.css">
	<link rel="stylesheet" type="text/css" href="{{ asset('member/css') }}/main.css">
<!--===============================================================================================-->
  
      <link rel="stylesheet" href="{{asset('bootstrap-sweetalert')}}/sweetalert.css" />


	<style>
		.wrap-login100 {
			padding: 130px 130px 130px 95px;
		}
	</style>
</head>

<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="{{ asset('member/images') }}/img-01.png" alt="IMG">
				</div>

                {!! Form::open(array('url' => 'member_panel/login', 'class' => 'login100-form validate-form')) !!}
                  
					<span class="login100-form-title">
						User Login
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Valid mobile is required">
						<input class="input100" type="text" name="mobile" required value="{{ old('mobile') }}" placeholder="mobile">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-phone" aria-hidden="true"></i>
						</span>
                      	@if ($errors->has('mobile'))
                            <span class="help-block" style="color:red">
                              <strong>{{ $errors->first('mobile') }} </strong>
                            </span>
                        @endif
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="password" required placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                      	@if ($errors->has('password'))
                          <span class="help-block" style="color:red">
                            <strong>{{ $errors->first('password') }} </strong>
                          </span>
                        @endif
					</div>
					
					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn">
							Login
						</button>
					</div>

				{!! Form::close() !!}
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="{{ asset('member/vendor') }}/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('member/vendor') }}/bootstrap/js/popper.js"></script>
	<script src="{{ asset('member/vendor') }}/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('member/vendor') }}/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('member/vendor') }}/tilt/tilt.jquery.min.js"></script>
  
  <script src="{{asset('bootstrap-sweetalert')}}/sweetalert.min.js"></script>

	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>

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
  
  <!--===============================================================================================-->
	<script src="{{ asset('member/js') }}/main.js"></script>

</body>
</html>