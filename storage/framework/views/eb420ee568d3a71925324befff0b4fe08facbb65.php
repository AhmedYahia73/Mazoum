<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="<?php echo e(asset('member/images')); ?>/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('member/vendor')); ?>/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('member/fonts')); ?>/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('member/vendor')); ?>/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('member/vendor')); ?>/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('member/vendor')); ?>/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('member/css')); ?>/util.css">
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('member/css')); ?>/main.css">
<!--===============================================================================================-->
  
      <link rel="stylesheet" href="<?php echo e(asset('bootstrap-sweetalert')); ?>/sweetalert.css" />


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
        }
      
        .container-login100 {
			    background: transparent !important;
        }
      
        body {
            background-image:url(<?php echo e(asset('member_cover2.jpeg')); ?>?<?php echo e(rand()); ?>);
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

	</style>
</head>

<body>
	
	<div class="limiter">
		<div class="container-login100">
          
          
			<div class="wrap-login100">
				

                <?php echo Form::open(array('url' => 'member_panel/login', 'class' => 'login100-form validate-form')); ?>

                  
              
                    <div style="text-align: center;margin-bottom: 0px;">
                      <img src="<?php echo e(asset('zerf.png')); ?>" style="width: 100px;">
                    </div>
              
					<span class="login100-form-title" style="padding-bottom: 15px;margin-top: 30px;">
						User Login
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Valid mobile is required">
						<input class="input100" type="text" name="mobile" required value="<?php echo e(old('mobile')); ?>" placeholder="mobile">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-phone" aria-hidden="true"></i>
						</span>
                      	<?php if($errors->has('mobile')): ?>
                            <span class="help-block" style="color:red">
                              <strong><?php echo e($errors->first('mobile')); ?> </strong>
                            </span>
                        <?php endif; ?>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="password" required placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
                      	<?php if($errors->has('password')): ?>
                          <span class="help-block" style="color:red">
                            <strong><?php echo e($errors->first('password')); ?> </strong>
                          </span>
                        <?php endif; ?>
					</div>
					
					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn" style="background: #1A3550;">
							Login
						</button>
					</div>

				<?php echo Form::close(); ?>

			</div>
                    
		</div>
      
	</div>
	
        	<div style="text-align: center;margin-top:0px"> <h2 style="color:#FFF;font-size:18px;margin-top: 20px;"> Mazoom Kwt </h2>   </div>

	

	
<!--===============================================================================================-->	
	<script src="<?php echo e(asset('member/vendor')); ?>/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('member/vendor')); ?>/bootstrap/js/popper.js"></script>
	<script src="<?php echo e(asset('member/vendor')); ?>/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('member/vendor')); ?>/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo e(asset('member/vendor')); ?>/tilt/tilt.jquery.min.js"></script>
  
  <script src="<?php echo e(asset('bootstrap-sweetalert')); ?>/sweetalert.min.js"></script>

	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>

  	<script>

        (function($){

            <?php if($message = Session::get('success')): ?>

                swal({
                    title: " ",
                    text: "<?php echo e($message); ?>",
                    type: "success",
                });

            <?php endif; ?>

            <?php if($message = Session::get('error')): ?>

                swal({
                    title: "",
                    text: "<?php echo e($message); ?>",
                    type: "warning",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-danger",
                });

            <?php endif; ?>


        })(jQuery);

    </script>
  
  <!--===============================================================================================-->
	<script src="<?php echo e(asset('member/js')); ?>/main.js"></script>

</body>
</html><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/member/login.blade.php ENDPATH**/ ?>