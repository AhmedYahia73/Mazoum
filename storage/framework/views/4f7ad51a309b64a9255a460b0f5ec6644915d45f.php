<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> <?php echo e($counter == 0 ? 'مسح صحيح' : 'مسح غير صحيح'); ?>  </title>
        <link href="<?php echo e(asset('auth/bootstrap.min.css')); ?>" rel="stylesheet">
      
      	<link rel="stylesheet" href="<?php echo e(asset('arabic_font')); ?>/droidarabickufi.css"/>


        <style>
          
          body {
          	background: #EEE;
          }
          
          body, html, h1, h2, h3, h4, h5, h6, p, li, .btn ,select , .btn-primary,
          .theme-green .back-bar .pointer-label , .custom_font
          {
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
          }

          ::-webkit-input-placeholder {
            /* Chrome/Opera/Safari */
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
          }
          ::-moz-placeholder {
            /* Firefox 19+ */
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
          }
          :-ms-input-placeholder {
            /* IE 10+ */
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
          }
          :-moz-placeholder {
            /* Firefox 18- */
            font-family: 'DroidArabicKufiRegular', sans-serif !important;
          }
          
          .custom_font {
          	    padding-left: 3%;
          	    padding-right: 3%;
          }
          
          @media(min-width:768px) {
            .card_desgin {
            	min-width: 1200px
            }
          }

         </style>

  
    </head>

    <body>
        <div class="vh-100 d-flex justify-content-center align-items-center">
          
          	<?php if($counter == 0): ?>
          
          	<div class="card card_desgin">
              <div class="card-header" style="text-align: center;background: #16AA68;color: #FFF;">
                
                	<img src="<?php echo e(asset('check.png')); ?>" style="max-width: 140px;margin-top: 30px;margin-bottom: 20px;">
                
                    <p style="margin-bottom: 20px;font-size: 22px;" class="custom_font">   
                		حياكم الله , أكتمـل حفلنا بحضوركم نتمنى لكم ليلة ممتعة
                	</p>
                
                	<div class="text-center" style="display: flex;justify-content: center;direction: rtl;">
                  
                  
                      <p class="custom_font">  
                        <span> <?php echo e($user_event->name); ?>  </span>
                      </p>


                      <p class="custom_font">  
                        <span>  دعوات :    </span>
                        <span> <?php echo e($user_event->users_count); ?> </span>


                      </p>

                      <p class="custom_font">  

                        <span> دخول : </span>
                        <span> <?php echo e($user_event->scan_count); ?>  </span>
                      </p>



                      <?php if($user_event->mobile): ?>
                      <p class="custom_font">  
                        <span> <?php echo e($user_event->mobile); ?>  </span>
                      </p>
                      <?php endif; ?>

                  </div>
                
              </div>
              <div class="card-body">
              	<div class="text-center" style="display: flex;justify-content: center;direction: rtl;">
                  
                 	<img src="<?php echo e(asset('scan_logo.png')); ?>?<?php echo e(rand()); ?>" style="height: 110px;padding: 15px 0;">
                  
                </div>
              </div>
            </div>
          
          	<?php else: ?>
          
          	<div class="card card_desgin" >
              <div class="card-header" style="text-align: center;background: #F6AE1A;color: #FFF;">
                
                	<img src="<?php echo e(asset('error.png')); ?>" style="max-width: 140px;margin-top: 30px;margin-bottom: 20px;">
                
                    <p style="font-size: 20px;">
                    	نأسف لكم لقد تجـاوزتم الحد الاقصي من الدعوات
                  	</p>
                
              </div>
              <div class="card-body" style="height: 125px;line-height: 100px;">
                
                <div class="text-center" style="display: flex;justify-content: center;direction: rtl;">
                  
                 	<img src="<?php echo e(asset('scan_logo2.png')); ?>?<?php echo e(rand()); ?>" style="height: 110px;padding: 15px 0;">
                  
                </div>
                
              	
              </div>
            </div>
          

          	<?php endif; ?>
     
      </div>
    </body>

</html>
<?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/welcome.blade.php ENDPATH**/ ?>