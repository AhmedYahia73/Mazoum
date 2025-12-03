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

<!--===============================================================================================-->

  <style>





/*//////////////////////////////////////////////////////////////////
[ FONT ]*/

@font-face {
  font-family: Poppins-Regular;
  src: url('../fonts/poppins/Poppins-Regular.ttf');
}

@font-face {
  font-family: Poppins-Bold;
  src: url('../fonts/poppins/Poppins-Bold.ttf');
}

@font-face {
  font-family: Poppins-Medium;
  src: url('../fonts/poppins/Poppins-Medium.ttf');
}

@font-face {
  font-family: Montserrat-Bold;
  src: url('../fonts/montserrat/Montserrat-Bold.ttf');
}

/*//////////////////////////////////////////////////////////////////
[ RESTYLE TAG ]*/

* {
	margin: 0px;
	padding: 0px;
	box-sizing: border-box;
}

body, html {
	height: 100%;
	font-family: Poppins-Regular, sans-serif;
}

/*---------------------------------------------*/
a {
	font-family: Poppins-Regular;
	font-size: 14px;
	line-height: 1.7;
	color: #666666;
	margin: 0px;
	transition: all 0.4s;
	-webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
}

a:focus {
	outline: none !important;
}

a:hover {
	text-decoration: none;
  color: #57b846;
}

/*---------------------------------------------*/
h1,h2,h3,h4,h5,h6 {
	margin: 0px;
}

p {
	font-family: Poppins-Regular;
	font-size: 14px;
	line-height: 1.7;
	color: #666666;
	margin: 0px;
}

ul, li {
	margin: 0px;
	list-style-type: none;
}


/*---------------------------------------------*/
input {
	outline: none;
	border: none;
}

textarea {
  outline: none;
  border: none;
}

textarea:focus, input:focus {
  border-color: transparent !important;
}

input:focus::-webkit-input-placeholder { color:transparent; }
input:focus:-moz-placeholder { color:transparent; }
input:focus::-moz-placeholder { color:transparent; }
input:focus:-ms-input-placeholder { color:transparent; }

textarea:focus::-webkit-input-placeholder { color:transparent; }
textarea:focus:-moz-placeholder { color:transparent; }
textarea:focus::-moz-placeholder { color:transparent; }
textarea:focus:-ms-input-placeholder { color:transparent; }

input::-webkit-input-placeholder { color: #999999; }
input:-moz-placeholder { color: #999999; }
input::-moz-placeholder { color: #999999; }
input:-ms-input-placeholder { color: #999999; }

textarea::-webkit-input-placeholder { color: #999999; }
textarea:-moz-placeholder { color: #999999; }
textarea::-moz-placeholder { color: #999999; }
textarea:-ms-input-placeholder { color: #999999; }

/*---------------------------------------------*/
button {
	outline: none !important;
	border: none;
	background: transparent;
}

button:hover {
	cursor: pointer;
}

iframe {
	border: none !important;
}


/*//////////////////////////////////////////////////////////////////
[ Utility ]*/
.txt1 {
  font-family: Poppins-Regular;
  font-size: 13px;
  line-height: 1.5;
  color: #999999;
}

.txt2 {
  font-family: Poppins-Regular;
  font-size: 13px;
  line-height: 1.5;
  color: #666666;
}


/*//////////////////////////////////////////////////////////////////
[ login ]*/

.limiter {
  width: 100%;
  margin: 0 auto;
}

.container-login100 {
  width: 100%;
  min-height: 100vh;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  padding: 15px;
  background: #9053c7;
  background: -webkit-linear-gradient(-135deg, #c850c0, #4158d0);
  background: -o-linear-gradient(-135deg, #c850c0, #4158d0);
  background: -moz-linear-gradient(-135deg, #c850c0, #4158d0);
  background: linear-gradient(-135deg, #c850c0, #4158d0);
}

.wrap-login100 {
  width: 960px;
  background: #fff;
  border-radius: 10px;

  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding: 177px 95px 33px 130px;
}

/*------------------------------------------------------------------
[  ]*/
.login100-pic {
  width: 316px;
}

.login100-pic img {
  max-width: 100%;
}


/*------------------------------------------------------------------
[  ]*/
.login100-form {
  width: 290px;
}

.login100-form-title {
  font-family: Poppins-Bold;
  font-size: 24px;
  color: #333333;
  line-height: 1.2;
  text-align: center;

  width: 100%;
  display: block;
  padding-bottom: 54px;
}


/*---------------------------------------------*/
.wrap-input100 {
  position: relative;
  width: 100%;
  z-index: 1;
  margin-bottom: 10px;
}

.input100 {
  font-family: Poppins-Medium;
  font-size: 15px;
  line-height: 1.5;
  color: #666666;

  display: block;
  width: 100%;
  background: #e6e6e6;
  height: 50px;
  border-radius: 25px;
  padding: 0 40px;
}


/*------------------------------------------------------------------
[ Focus ]*/
.focus-input100 {
  display: block;
  position: absolute;
  border-radius: 25px;
  bottom: 0;
  right: 0;
  z-index: -1;
  width: 100%;
  height: 100%;
  box-shadow: 0px 0px 0px 0px;
  color: rgba(87,184,70, 0.8);
}

.input100:focus + .focus-input100 {
  -webkit-animation: anim-shadow 0.5s ease-in-out forwards;
  animation: anim-shadow 0.5s ease-in-out forwards;
}

@-webkit-keyframes anim-shadow {
  to {
    box-shadow: 0px 0px 70px 25px;
    opacity: 0;
  }
}

@keyframes anim-shadow {
  to {
    box-shadow: 0px 0px 70px 25px;
    opacity: 0;
  }
}

.symbol-input100 {
  font-size: 15px;

  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  align-items: center;
  position: absolute;
  border-radius: 25px;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  padding-right: 20px;
  pointer-events: none;
  color: #666666;

  -webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
  transition: all 0.4s;
}

.input100:focus + .focus-input100 + .symbol-input100 {
  color: #57b846;
  padding-right: 28px;
}

/*------------------------------------------------------------------
[ Button ]*/
.container-login100-form-btn {
  width: 100%;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  padding-top: 20px;
}

.login100-form-btn {
  font-family: Montserrat-Bold;
  font-size: 15px;
  line-height: 1.5;
  color: #fff;
  text-transform: uppercase;

  width: 100%;
  height: 50px;
  border-radius: 25px;
  background: #57b846;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0 25px;

  -webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
  transition: all 0.4s;
}

.login100-form-btn:hover {
  background: #333333;
}



/*------------------------------------------------------------------
[ Responsive ]*/



@media (max-width: 992px) {
  .wrap-login100 {
    padding: 177px 85px 33px 90px;
  }

  .login100-pic {
    width: 35%;
  }

  .login100-form {
    width: 50%;
  }
}

@media (max-width: 768px) {
  .wrap-login100 {
    padding: 100px 80px 33px 80px;
  }

  .login100-pic {
    display: none;
  }

  .login100-form {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .wrap-login100 {
    padding: 100px 15px 33px 15px;
  }
}


/*------------------------------------------------------------------
[ Alert validate ]*/

.validate-input {
  position: relative;
}

.alert-validate::before {
  content: attr(data-validate);
  position: absolute;
  max-width: 70%;
  background-color: white;
  border: 1px solid #c80000;
  border-radius: 13px;
  padding: 4px 10px 4px 25px;
  top: 50%;
  -webkit-transform: translateY(-50%);
  -moz-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  -o-transform: translateY(-50%);
  transform: translateY(-50%);
  left: 8px;
  pointer-events: none;

  font-family: Poppins-Medium;
  color: #c80000;
  font-size: 13px;
  line-height: 1.4;
  text-align: right;

  visibility: hidden;
  opacity: 0;

  -webkit-transition: opacity 0.4s;
  -o-transition: opacity 0.4s;
  -moz-transition: opacity 0.4s;
  transition: opacity 0.4s;
}

.alert-validate::after {
  content: "\f06a";
  font-family: FontAwesome;
  display: block;
  position: absolute;
  color: #c80000;
  font-size: 15px;
  top: 50%;
  -webkit-transform: translateY(-50%);
  -moz-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  -o-transform: translateY(-50%);
  transform: translateY(-50%);
  left: 13px;
}

.alert-validate:hover:before {
  visibility: visible;
  opacity: 1;
}

@media (max-width: 992px) {
  .alert-validate::before {
    visibility: visible;
    opacity: 1;
  }
}

  </style>

      <link rel="stylesheet" href="{{asset('bootstrap-sweetalert')}}/sweetalert.css" />


	<style>
		.wrap-login100 {
			padding: 130px 130px 130px 95px;
		}

      	.wrap-login100 {
   		 	width: auto !important;
      	}

      	.wrap-login100 {
            padding: 45px !important;
            padding-top: 25px !important;
            padding-bottom: 25px !important;
            margin-top: 100px;
          	box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        }

        .container-login100 {
			    background: transparent !important;
        }

        body {
            background-image:url({{ asset('event-background-v2.jpg') }}?{{ rand() }});
      		background-size: cover;
    		background-repeat: no-repeat;
            overflow: hidden;
        }

      .container-login100 {
      	min-height:auto
      }


      .login100-form {
          width: 100%;
      }

      html , body {
      	direction:rtl;
        text-align:right
      }

      .please_mobile {
          font-size: 20px;
          font-weight: 600;
          margin-bottom: 10px;
      }

      @media (max-width: 768px) {

        .please_mobile {
            font-size: 18px;
        }

      }


	</style>

     <link href="{{asset('intlTelInput')}}/css/intlTelInput.css" rel="stylesheet" type="text/css" />

  	<style>
      .iti__country-list { color: #000; left: 0 !important }

        #phone {
            padding-right: .75rem;
                font-size: 18px;
    color: #000;
            direction: ltr
        }

        #phone::-webkit-input-placeholder {
            direction: rtl;
            text-align: right
        }
        #phone::-moz-placeholder {
            direction: rtl;
            text-align: right
        }
        #phone:-ms-input-placeholder {
            direction: rtl;
            text-align: right
        }
        #phone:-moz-placeholder {
            direction: rtl;
            text-align: right
        }

        .iti__country , .iti--separate-dial-code .iti__selected-flag { direction: ltr }

        .iti { display: flex }

        .mb-5, .my-5 {
            margin-bottom: 0 !important;
            margin-top: 0 !important;
        }

        .form-control:focus { background-color: transparent !important }

         .iti--separate-dial-code .iti__selected-flag {
    background: transparent;
    border-right: 1px solid #EEEE;
}

        ul li {
            text-align: right !important
        }

        .checkmark {
            font-size: 15px
        }

        @media (max-width: 768px) {
            ul li {
                text-align: right !important;
                font-size: 13px;
                margin-bottom: 8px;
                line-height: 1.6;
            }

            .promise label {
                display: inline;
                font-size: 15px !important;
            }
        }

        .un_active {
            display: none
        }
  </style>

</head>

<body>

	<div class="limiter">
		<div class="container-login100">


			<div class="wrap-login100">


                {!! Form::open(array('url' => 'event/login', 'class' => 'login100-form validate-form')) !!}


                    <div style="text-align: center;margin-bottom: 0px;">
                      <h1 style="
    font-weight: bold;
    font-size: 30px;
">
                        حياك في معزوم الكويت
                       </h1>

                      <h2 style="
    font-size: 20px;
    font-weight: bold;
    margin-top: 16px;
">
                        الوجهــــة الأولى للدعــــوات الإلكتروانيـــة
                      </h2>
                    </div>


					<div style="margin-bottom: 0px;margin-top: 45px;text-align: right;">
                      <h3 style="display:none;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 10px;
">
                        	تسجيـل الدخول
                      </h3>
                      <h3 class="please_mobile">
الرجـاء إدخـال رقـم الهاتـف لأظهـار الدعـوة
                      </h3>
                    </div>

					<div class="wrap-input100 validate-input" data-validate = "Valid mobile is required">

                      <input class="input100" style="border-radius: 0;" type="tel" id="phone" name="phone" required value="{{ old('mobile') }}" placeholder=" رقم الموبيل ">

                      <span class="focus-input100"></span>

                      <input type="hidden" id="country" name="country" value="kwt">
                      <input type="hidden" id="code" name="mobile_code" value="965">

                      @if ($errors->has('mobile'))
                      <span class="help-block" style="color: #b30000;">
                        <strong>{{ $errors->first('mobile') }} </strong>
                      </span>
                      @endif


					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn" style="background: #162D2C;border-radius: 0;font-size: 24px;font-weight: bold;">

                          <i class="fa fa-sign-in" aria-hidden="true"></i>
                          <span style="
    display: inline-block;
    margin-left: 10px;
    margin-right: 10px;
"> دخـــول </span>


						</button>
					</div>

              		<div style="text-align: center;margin-bottom: 0px;">

                      <p style="
    font-size: 21px;
    color: #000;
    font-weight: bold;
    margin-top: 15px;
"> جميـع الحقــوق محفوظـة </p>


                      <p style="
    font-size: 21px;
    color: #000;
    font-weight: bold;
    margin-top: 0px;
"> Mazoom Kwt </p>

                    </div>


				{!! Form::close() !!}
			</div>

		</div>

	</div>

        	<div style="text-align: center;margin-top:0px"> <h2 style="color:#FFF;font-size:18px;margin-top: 20px;"> Mazoom Kwt </h2>   </div>




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

      <script src="{{asset('intlTelInput')}}/js/intlTelInput.js" type="text/javascript"></script>

   <script>

        $(document).ready(function() {

            var phone_number = window.intlTelInput(document.querySelector("#phone"), {
                separateDialCode: true,
                preferredCountries:["in"],
                hiddenInput: "full",
                initialCountry: "kw",
                utilsScript: "{{asset('intlTelInput')}}/js/utils.js",

            });

            var input = document.querySelector('#phone');
            var iti = window.intlTelInputGlobals.getInstance(input);
            iti.isValidNumber();

            input .addEventListener("countrychange", function() {
                var stringify =JSON.stringify(iti.getSelectedCountryData());

                $('#country').val(JSON.parse(stringify).iso2);
                $('#code').val(JSON.parse(stringify).dialCode);

            });


        });

   </script>


</body>
</html>
