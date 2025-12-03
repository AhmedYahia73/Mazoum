<?php
    $lang = app()->getLocale();
?>




<?php $__env->startSection('header'); ?>

	<style>
      .main-banner.inner-banner:before , .site-branding a{
      	display:none
      }  
	</style>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>



    <!-- Banner Start -->
	<section class="main-banner inner-banner back-img" id="main-banner" style="background-image: url('<?php echo e(asset('logo/about-us.jpg')); ?>');">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="banner-content" style="display:none">
						<h1 class="h1-title wow fadeup-animation" data-wow-delay="0.2s"> عن التطبيق </h1>
						<div class="breadcrumb-box wow fadeup-animation" data-wow-delay="0.4s">
							<ul>
								<li><a href="<?php echo e(route($lang.'_home')); ?>" title="Home"> <?php echo e(trans('home.Home')); ?> </a></li>
								<li> من نحن </li>
							</ul>
						</div>
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
							<p style="
    text-align: justify;
">
								
                              تطبيــق معـــــزوم الكويت عبارة عن بطاقات دعوات إلكترونية رقمية خاصة بالمناسبات والحفلات وبخدمات تقنية جديدة بحيث تحمل بطاقة كل ضيف ( QR ) خاص بها وتمرر عبر تطبيق معزوم الكويت من خلال المشرفين والمشرفات لدينا
                            </p>
							<ul>
								<li><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> تكاليف مادية أقل بكثير من البطاقات الورقية </li>
								<li><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> سهولة توزيع البطاقات وتوفير الجهد والوقت  </li>
								<li><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> بطاقات دعوات إلكترونية بتصاميم جديدة عالية الجودة  </li>
								<li><span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span> الاستغناء التام عن بطاقات الدعوات التقليدية  </li>
                            </ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- About Us End -->

	<!-- Who We Are Start -->
	<section class="main-who-we-are" style="padding-bottom: 50px">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 order-lg-1 order-2">
					<div class="who-we-are-content wow right-animation" data-wow-delay="0.2s">
						<span class="sub-title"> من نكون </span>
						<h2 class="h2-title" style="
    font-size: 30px;
    line-height: 1.3;
    font-weight: 600;
    margin-bottom: 10px;
">
                      		تطبيــق معـــــزوم الكويت لإدارة وتنفيــــذ الدعـــوات الإلكتروانية
                        </h2>
                        <p>
                        	تطبيقنا هو منصة رائدة تقدم تجربة فريدة لخلق بطاقات دعوة إلكترونية رقمية، مصممة خصيصًا للمناسبات والحفلات. يتيح للمستخدمين إضافة لمسات شخصية وإبداعية إلى دعواتهم، مما يجعل كل حدث فريدًا ولا يُنسى.  
                        </p>
						<div class="who-we-are-list">
							<div class="who-we-are-list-box">
								<div class="who-we-are-list-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/our-vision-icon.png" alt="Our Vision">
								</div>
								<div class="who-we-are-list-text">
									<h3 class="h3-title"> رؤيتنا </h3>
									<p>
                                  		تهدف رؤيتنا إلى تحويل تجارب المناسبات والحفلات إلى لحظات لا تُنسى، من خلال تقديم تجربة فريدة ومبتكرة للمستخدمين من خلال بطاقات الدعوة الإلكترونية. نسعى إلى أن نكون رفيق الأفراح للناس ونساهم في جعل لحظاتهم الخاصة أكثر جمالًا وتألقًا.
                                    </p>
								</div>
							</div>
							<div class="who-we-are-list-box">
								<div class="who-we-are-list-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/our-mission-icon.png" alt="Our Mission">
								</div>
								<div class="who-we-are-list-text">
									<h3 class="h3-title"> مهمتنا</h3>
									<p>
                                    	تتمثل مهمتنا في تقديم منصة سهلة الاستخدام ومبتكرة لإنشاء بطاقات دعوة إلكترونية فريدة وشخصية. نسعى جاهدين لتحقيق تواصل فعّال وأنيق بين المستخدمين وضيوفهم، حيث يتمكنون من نقل أجواء الحدث من خلال تصاميم مبتكرة وتقنيات متقدمة
                                    </p>
								</div>
							</div>
							<div class="who-we-are-list-box">
								<div class="who-we-are-list-icon">
									<img src="<?php echo e(asset('website/assets/images')); ?>/our-goals-icon.png" alt="Our Goals">
								</div>
								<div class="who-we-are-list-text">
									<h3 class="h3-title"> أهدافنا </h3>
									<div>
                                  		<ol>
                                          <li style="margin-bottom: 10px;"> تقديم تجربة مستخدم مميزة: نسعى لتوفير واجهة سهلة الاستخدام ومحتوى ملهم للمستخدمين لإنشاء بطاقات دعوة فريدة وجميلة. </li>
                                          <li style="margin-bottom: 10px;"> الابتكار والتطوير المستمر: نحن نسعى جاهدين للبقاء على اطلاع دائم على أحدث التقنيات والاتجاهات في تصميم الدعوات الإلكترونية لضمان تقديم خدمة متطورة ومبتكرة. </li>
                                          <li style="margin-bottom: 10px;"> رضا العملاء: نحن ملتزمون بتلبية توقعات واحتياجات عملائنا، ونسعى جاهدين لتحقيق رضاهم التام من خلال تقديم خدمة عملاء استثنائية. </li>
                                          <li style="margin-bottom: 10px;"> التأثير الاجتماعي: نطمح إلى أن نكون عامل إيجابي في تعزيز التواصل الاجتماعي والتفاعل بين الأفراد والمجتمعات من خلال تسهيل التواصل والاحتفال بالمناسبات الخاصة. </li>
                                        </ol>
                                    </div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 order-lg-2 order-1">
					<div class="who-we-are-img wow left-animation" data-wow-delay="0.2s">
						<img src="<?php echo e(asset('logo/vision.png')); ?>?<?php echo e(rand()); ?>" alt="Who We Are">
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Who We Are End -->




<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mazoom-kw/htdocs/mazoom-kw.com/project-app/resources/views/user/pages/about-us.blade.php ENDPATH**/ ?>