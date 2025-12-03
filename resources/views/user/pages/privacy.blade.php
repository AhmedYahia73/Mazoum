@extends('user.layouts.master')


@section('header')

@endsection


@section('content')

<!-- Banner Start -->
	<section class="main-banner inner-banner back-img" id="main-banner" style="background-image: url('{{ asset('website/assets/images') }}/faq-banner.jpg');">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="banner-content">
						<h1 class="h1-title wow fadeup-animation" data-wow-delay="0.2s">FAQ</h1>
						<div class="breadcrumb-box wow fadeup-animation" data-wow-delay="0.4s">
							<ul>
								<li><a href="index.html" title="Home">Home</a></li>
								<li>FAQ</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Banner End -->

	<!-- FAQ Content Start -->
	<section class="main-faq-content">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<div class="faq-title">
						<span class="sub-title">FAQ</span>
						<h2 class="h2-title">What Is Shivaa?</h2>
					</div>
					<div class="faq-accordion">
						<div class="accordion" id="accordionExample">
							<div class="card wow fadeup-animation" data-wow-delay="0.2s">
								<div class="card-header" id="headingOne">
									<h3 class="h3-title" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><span>Proin fermentum eleifend arcu, quis rhoncus augue aliquet nec ?</span> <span class="icon"><i class="fa fa-angle-left" aria-hidden="true"></i></span></h3>
								</div>
								<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
									<div class="card-body">
										<p>Nam eu fringilla est, eu posuere ex. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur in pretium tortor, mollis ultricies felis. Aliquam fermentum lacus et ex tincidunt, non pulvinar nisl elementum.</p>
									</div>
								</div>
							</div>
							<div class="card wow fadeup-animation" data-wow-delay="0.3s">
								<div class="card-header" id="headingTwo">
									<h3 class="h3-title collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"><span>Maecenas nec enim non mauris pretium consectetur sit amet at nisl ?</span> <span class="icon"><i class="fa fa-angle-left" aria-hidden="true"></i></span></h3>
								</div>
								<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
									<div class="card-body">
										<p>Nam eu fringilla est, eu posuere ex. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur in pretium tortor, mollis ultricies felis. Aliquam fermentum lacus et ex tincidunt, non pulvinar nisl elementum.</p>
									</div>
								</div>
							</div>
							<div class="card wow fadeup-animation" data-wow-delay="0.4s">
								<div class="card-header" id="headingThree">
									<h3 class="h3-title collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"><span>Duis dictum leo in semper dictum ?</span> <span class="icon"><i class="fa fa-angle-left" aria-hidden="true"></i></span></h3>
								</div>
								<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
									<div class="card-body">
										<p>Nam eu fringilla est, eu posuere ex. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur in pretium tortor, mollis ultricies felis. Aliquam fermentum lacus et ex tincidunt, non pulvinar nisl elementum.</p>
									</div>
								</div>
							</div>
							<div class="card wow fadeup-animation" data-wow-delay="0.5s">
								<div class="card-header" id="headingFour">
									<h3 class="h3-title collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour"><span>Phasellus facilisis, sapien vitae gravida vehicula, nisl mauris rhoncus ?</span> <span class="icon"><i class="fa fa-angle-left" aria-hidden="true"></i></span></h3>
								</div>
								<div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
									<div class="card-body">
										<p>Nam eu fringilla est, eu posuere ex. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur in pretium tortor, mollis ultricies felis. Aliquam fermentum lacus et ex tincidunt, non pulvinar nisl elementum.</p>
									</div>
								</div>
							</div>
							<div class="card wow fadeup-animation" data-wow-delay="0.6s">
								<div class="card-header" id="headingFive">
									<h3 class="h3-title collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive"><span>Nunc sit amet orci eget justo vehicula congue a sit amet lectus ?</span> <span class="icon"><i class="fa fa-angle-left" aria-hidden="true"></i></span></h3>
								</div>
								<div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordionExample">
									<div class="card-body">
										<p>Nam eu fringilla est, eu posuere ex. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur in pretium tortor, mollis ultricies felis. Aliquam fermentum lacus et ex tincidunt, non pulvinar nisl elementum.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- FAQ Content End -->

@endsection
