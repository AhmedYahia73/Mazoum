<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> {{ $counter == 0 ? 'مسح صحيح' : 'مسح غير صحيح' }}  </title>
        <link href="{{ asset('auth/bootstrap.min.css') }}" rel="stylesheet">
      
      	<link rel="stylesheet" href="{{ asset('arabic_font') }}/droidarabickufi.css"/>


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
          
          	@if($counter == 0)
          
          	<div class="card card_desgin">
              <div class="card-header" style="text-align: center;background: #16AA68;color: #FFF;">
                
                	<img src="{{ asset('check.png') }}" style="max-width: 140px;margin-top: 30px;margin-bottom: 20px;">
                
                    <p style="margin-bottom: 20px;font-size: 22px;" class="custom_font">   
                		حياكم الله , أكتمـل حفلنا بحضوركم نتمنى لكم ليلة ممتعة
                	</p>
                
                	<div class="text-center" style="display: flex;justify-content: center;direction: rtl;">
                  
                  
                      <p class="custom_font">  
                        <span> {{ $user_event->name }}  </span>
                      </p>


                      <p class="custom_font">  
                        <span>  دعوات :    </span>
                        <span> {{ $user_event->users_count }} </span>


                      </p>

                      <p class="custom_font">  

                        <span> دخول : </span>
                        <span> {{ $user_event->scan_count }}  </span>
                      </p>



                      @if($user_event->mobile)
                      <p class="custom_font">  
                        <span> {{ $user_event->mobile }}  </span>
                      </p>
                      @endif

                  </div>
                
              </div>
              <div class="card-body">
              	<div class="text-center" style="display: flex;justify-content: center;direction: rtl;">
                  
                 	<img src="{{ asset('scan_logo.png') }}?{{ rand() }}" style="height: 110px;padding: 15px 0;">
                  
                </div>
              </div>
            </div>
          
          	@else
          
          	<div class="card card_desgin" >
              <div class="card-header" style="text-align: center;background: #F6AE1A;color: #FFF;">
                
                	<img src="{{ asset('error.png') }}" style="max-width: 140px;margin-top: 30px;margin-bottom: 20px;">
                
                    <p style="font-size: 20px;">
                    	نأسف لكم لقد تجـاوزتم الحد الاقصي من الدعوات
                  	</p>
                
              </div>
              <div class="card-body" style="height: 125px;line-height: 100px;">
                
                <div class="text-center" style="display: flex;justify-content: center;direction: rtl;">
                  
                 	<img src="{{ asset('scan_logo2.png') }}?{{ rand() }}" style="height: 110px;padding: 15px 0;">
                  
                </div>
                
              	
              </div>
            </div>
          

          	@endif
     
      </div>
    </body>

</html>
