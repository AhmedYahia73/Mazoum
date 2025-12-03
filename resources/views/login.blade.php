<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="{{ asset('auth/images') }}/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('auth/vendor') }}/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('auth/fonts') }}/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('auth/vendor') }}/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('auth/vendor') }}/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('auth/vendor') }}/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('auth/css') }}/util.css">
	<link rel="stylesheet" type="text/css" href="{{ asset('auth/css') }}/main.css">
<!--===============================================================================================-->

    <link rel="stylesheet" href="{{asset('bootstrap-sweetalert')}}/sweetalert.css" />

</head>
<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="{{ asset('auth/images') }}/img-01.png" alt="IMG">
				</div>

				<form class="login100-form validate-form" action="{{ asset('login') }}" method="post">

                    {{csrf_field()}}

					<span class="login100-form-title">
						Login Form
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="email" name="email" value="{{ old('email') }}" required placeholder="Email">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
                        @if ($errors->has('email'))
                            <span class="help-block" style="color:red">
                                <strong>{{ $errors->first('email') }} </strong>
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

				</form>
			</div>
		</div>
	</div>




<!--===============================================================================================-->
	<script src="{{ asset('auth/vendor') }}/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('auth/vendor') }}/bootstrap/js/popper.js"></script>
	<script src="{{ asset('auth/vendor') }}/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('auth/vendor') }}/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ asset('auth/vendor') }}/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="{{ asset('auth/js') }}/main.js"></script>

    <script src="{{asset('bootstrap-sweetalert')}}/sweetalert.min.js"></script>

    <script>

        (function($){

            @if ($message = Session::get('success'))

                swal({
                    title: " ",
                    text: "{{ $message }}",
                    imageUrl: '{{ asset('img/sent.jpg') }}'
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
