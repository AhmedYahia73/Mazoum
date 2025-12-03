<?php
    $lang = app()->getLocale();
?>




<?php $__env->startSection('header'); ?>

    <style>
        .slick-dotted.slick-slider {
            direction: ltr;
        }

      .h1-title .typer {
          font-size: 35px !important;
      }

      #main-banner {
        background-image: url(<?php echo e(asset('logo/home-cover.jpg')); ?>) !important;
      }
      
      #main-banner .h1-title {
          font-size: 45px;
          line-height: 50px;
      }
      
      .h1-title .typer {
          font-size: 26px !important;
      }
    </style>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>


    <!-- Banner Start -->
    <section class="main-banner back-img" id="main-banner" style="background-image: url('<?php echo e(asset('website/assets/images')); ?>/banner-bg.jpg');">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="banner-content">
                      	<h1 class="h1-title wow fadeup-animation" data-wow-delay="0.4s">
                          معـــــزوم الكويت
                        </h1>
                        <h1 class="h1-title wow fadeup-animation" data-wow-delay="0.4s">
                          <span class="typer"></span>
                        </h1>
                        <p class="wow fadeup-animation" data-wow-delay="0.5s">
                      		تكنولوجيـا حديثـة ومتطـورة عن طريـــق الباركـود الشخصـي
                        </p>
                        
                        <div class="banner-btn wow fadeup-animation" data-wow-delay="0.6s">
                            <a href="<?php echo e(asset('أتصل-بنا')); ?>" title="Contact Us Now" class="sec-btn"><span> تواصل معنا </span></a>
                            <a href="https://www.youtube.com/watch?v=MwSDkhuK5Pw" class="play-video popup-youtube-nnnn"><span class="icon"><i class="fa fa-play" aria-hidden="true"></i></span> شغل الفيديو </a>
                        </div>
                      
                      	<div>
                            <a href="" target="_blank" style="display: inline-block;">
                            	<img src="<?php echo e(asset('google.png')); ?>" style="max-width: 300px;margin-top: 25px;height: 75px;">  
                            </a>
                          	<a href="https://apps.apple.com/kw/app/mazoom-معزوم/id6450513282" target="_blank"  style="display: inline-block;">
                            	<img src="<?php echo e(asset('apple.png')); ?>" style="max-width: 300px;margin-top: 25px;height: 75px;">  
                            </a>
                        </div>
                      
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="banner-img wow fadeup-animation" data-wow-delay="0.5s">
                        <img src="<?php echo e(asset('logo/home-v2.png')); ?>" alt="Banner Image">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner End -->

    <!-- About Us Start -->
	<section class="main-about-us">
		<div class="container">
			<div class="row align-items-center" id="about-us">
				<div class="col-lg-6">
					<div class="main-about-img wow right-animation" data-wow-delay="0.4s">
						<div class="about-img-box">
							<div class="about-img img_hover back-img" style="background-image: url('<?php echo e(asset('logo/about-cover.jpg')); ?>?<?php echo e(rand()); ?>');"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="about-content wow left-animation" data-wow-delay="0.4s">
						<h2 class="h2-title"> عن التطبيق </h2>
                        <div class="about-text">
							<p>

                              تطبيــق معـــــزوم الكويت عبارة عن بطاقات دعوات إلكترونية رقمية خاصة بالمناسبات والحفلات وبخدمات تقنية جديدة بحيث تحمل بطاقة كل ضيف ( QR ) خاص بها وتمرر عبر تطبيق معزوم الكويت من خلال المشرفين والمشرفات لدينا
                            </p>
							<ul>
								<li><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> تكاليف مادية أقل بكثير من البطاقات الورقية </li>
								<li><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> سهولة توزيع البطاقات وتوفير الجهد والوقت  </li>
								<li><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> بطاقات دعوات إلكترونية بتصاميم جديدة عالية الجودة  </li>
								<li><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> الاستغناء التام عن بطاقات الدعوات التقليدية  </li>
                            </ul>
						</div>
						<a href="<?php echo e(asset('من-نحن')); ?>" class="sec-btn" title="Contact Us Now"><span> المزيد </span></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- About Us End -->


    <!-- Whatch Us Start -->
    <section class="main-whatch-us">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="whatch-title">
                        <span class="sub-title"> شاهدنا </span>
                        <h2 class="h2-title"> انظر كيف نعمل </h2>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-9">
                    <div class="whatch-video-box wow fadeup-animation" data-wow-delay="0.4s">
                        <div class="whatch-video-img back-img img_hover" style="background-image: url('http://img.youtube.com/vi/MwSDkhuK5Pw/0.jpg');">
                            <a href="https://www.youtube.com/watch?v=MwSDkhuK5Pw" class="video-play-icon popup-youtube-nnn" title="Play Video"><span><i class="fa fa-play" aria-hidden="true"></i></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Whatch Us End -->


    <?php
        $Pricing = App\Models\Pricing::get();
    ?>


    <?php if($Pricing != null && $Pricing->count() > 0): ?>
    <!-- Our Pricing Start -->
    <section class="main-pricing">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="pricing-title">
                        <span class="sub-title"> <?php echo e(trans('home.Pricing')); ?> </span>
                        <h2 class="h2-title"> <?php echo e($lang == 'en' ? 'Choose Price Plan' : 'اختر خطة الأسعار'); ?> </h2>
                    </div>
                </div>
            </div>

            <?php echo $__env->make('user.includes.pricing', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>
    </section>
    <!-- Our Pricing End -->
    <?php endif; ?>



    <?php
        $Desgins = App\Models\WebDesgins::get();
    ?>

    <?php if($Desgins != null && $Desgins->count() > 0): ?>
	<!-- Portfolio Tabbing Start -->
	<div class="main-portfolio-tabbing">
		<div class="container">

            <div class="row">
                <div class="col-12">
                    <div class="project-title">
                        <span class="sub-title" id="portfolio"><?php echo e(trans('home.Desgins')); ?></span>
                        <h2 class="h2-title"><?php echo e($lang == 'en' ? 'Our Work Portfolio' : 'اعمالنا'); ?></h2>
                    </div>
                </div>
            </div>

            <?php echo $__env->make('user.includes.desgins', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		</div>
	</div>
	<!-- Portfolio Tabbing End -->
    <?php endif; ?>



    <!-- Testimonial Start -->
    <section class="main-testimonial">
        <div class="testimonial-animate">
            <div class="testimonial-img testimonial-img1 back-img" style="background-image: url('<?php echo e(asset('logo/so-1.png')); ?>?<?php echo e(rand()); ?>');"></div>
            <div class="testimonial-img testimonial-img2 back-img" style="background-image: url('<?php echo e(asset('logo/so-2.png')); ?>?<?php echo e(rand()); ?>');"></div>
            <div class="testimonial-img testimonial-img3 back-img" style="background-image: url('<?php echo e(asset('logo/so-3.png')); ?>?<?php echo e(rand()); ?>');"></div>
            <div class="testimonial-img testimonial-img4 back-img" style="background-image: url('<?php echo e(asset('logo/so-4.png')); ?>?<?php echo e(rand()); ?>');"></div>
            <div class="testimonial-img testimonial-img5 back-img" style="background-image: url('<?php echo e(asset('logo/so-5.png')); ?>?<?php echo e(rand()); ?>');"></div>
            <div class="testimonial-img testimonial-img6 back-img" style="background-image: url('<?php echo e(asset('logo/so-1.png')); ?>?<?php echo e(rand()); ?>');"></div>>
            <div class="testimonial-img testimonial-img7 back-img" style="background-image: url('<?php echo e(asset('logo/so-2.png')); ?>?<?php echo e(rand()); ?>');"></div>
            <div class="testimonial-img testimonial-img8 back-img" style="background-image: url('<?php echo e(asset('logo/so-3.png')); ?>?<?php echo e(rand()); ?>');"></div>>
            <div class="testimonial-img testimonial-img9 back-img" style="background-image: url('<?php echo e(asset('logo/so-4.png')); ?>?<?php echo e(rand()); ?>');"></div>
            <div class="testimonial-img testimonial-img10 back-img" style="background-image: url('<?php echo e(asset('logo/so-5.png')); ?>?<?php echo e(rand()); ?>');"></div>v>
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="testimonial-title">
                        <h2 class="h2-title"> ما يقوله عملائنا </h2>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-8">
                    <div class="main-testimonial-box">
                        <span class="quote">
                            <img src="<?php echo e(asset('website/assets')); ?>/images/quote-icon.png" alt="Quote Icon">
                        </span>
                        <div class="main-testimonial-slider back-img" style="background-image: url('<?php echo e(asset('website/assets/images')); ?>/testimonial-bg.jpg');">
                            <div class="testimonial-slider">
                                <div class="testimonial-box">
                                    <div class="testimonial-text">
                                        <p>
                                          كان التصميم مذهل، واستمتعت أصدقائي وعائلتي بتلقي الدعوة الرقمية بكل فخر. شكراً لكم على جعل يومنا الخاص أكثر جمالاً!
                                        </p>
                                    </div>
                                    <div class="review-by">
                                        <h3 class="h3-title"> مريم الهاجري </h3>
                                    </div>
                                </div>
                                <div class="testimonial-box">
                                    <div class="testimonial-text">
                                        <p> تطبيق رائع وسهل الاستخدام. استخدمته لإرسال دعوة لحفل عيد ميلادي، وكانت الردود من الأصدقاء سريعة جداً. أعجبوا بالتصميم الجميل وفكرة الاحتفال الرقمي. أنصح به بشدة </p>
                                    </div>
                                    <div class="review-by">
                                        <h3 class="h3-title">  مشاعل العتيبي </h3>
                                    </div>
                                </div>
                                <div class="testimonial-box">
                                    <div class="testimonial-text">
                                        <p>  أحببت كيف أمكنني تخصيص بطاقتي بسهولة. كانت الخيارات كثيرة، والنتيجة كانت مثالية. أعجبت أصدقائي بالفعل بالفكرة وسأستخدم التطبيق مرة أخرى في المستقبل. شكراً لكم </p>
                                    </div>
                                    <div class="review-by">
                                        <h3 class="h3-title"> نوال المطيري  </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonial End -->


    <!-- Get In Touch Start -->
    <section class="main-contact">
        <div class="container">
            <div class="row align-items-center" id="contact">

                <div class="col-lg-6 order-lg-1 order-2">

                    <?php echo $__env->make('user.includes.contact', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>

                <div class="col-lg-6 order-lg-2 order-1">
                    <div class="contact-text wow left-animation" data-wow-delay="0.4s">
                        <span class="sub-title"> ابقى على تواصل </span>
                        <div class="contact-link">
                            <p> اتصل بنا للحصول على دعم سريع لهذا الرقم. </p>
                            <a href="tel:96597378181" title="+96597378181" style="direction: ltr;display: block;"> +96597378181 </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Get In Touch End -->



<?php $__env->stopSection(); ?>


<?php $__env->startSection('footer'); ?>

	<script>

        (function($){

            // Typed Js Start
            $(".typer").typed({
                /*strings: [" معزوم الكويت للدعوات الالكتروانيه ", "لإدارة وتنفيــــذ الدعـــوات الإلكتروانية", "الخاصـة بالمناسبـــات والأفـــــراح"],*/
              	strings: [" معزوم الكويت للدعوات الالكتروانيه "],
                typeSpeed: 200,
                backSpeed: 30,
                backDelay: 500,
                cursorChar: "",
                contentType: 'html',
                loop: true
            });
            // Typed Js End


        })(jQuery);

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/layouts/home.blade.php ENDPATH**/ ?>