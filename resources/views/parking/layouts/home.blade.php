@extends('parking.layouts.master')

@section('title',trans('home.home'))

@section('content')

    <div class="row">

        <!-- Website Analytics-->
        <div class="col-lg-12 col-md-12 mb-4">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center" style="margin-bottom: 15px;">
            <h5 class="card-title mb-0">
                مرحبا بك يا
                {{ auth()->guard('parking')->user()->name }}
            </h5>
            </div>


        </div>
        </div>

    </div>

    <div class="row">

        <div class="col-xl-4 col-4" style="margin-bottom:20px">
            <div class="card">
                <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                    <h2>
                        {{ \App\Models\Parking::where('parking_status', 'starting')->count() }}
                    </h2>
                    <span class="text-muted">
                        عدد دخول باركينج
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-4" style="margin-bottom:20px">
            <div class="card">
                <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                    <h2>
                        {{ \App\Models\Parking::where('parking_status', 'leaving')->count() }}
                    </h2>
                    <span class="text-muted">
                        عدد طلبات الخروج
                    </span>
                </div>
            </div>
        </div>
      
      
      	<div class="col-xl-4 col-4" style="margin-bottom:20px">
            <div class="card">
                <div class="card-body text-center pb-0" style="position: relative;padding-bottom: 20px !important;">
                    <h2>
                        {{ \App\Models\Parking::where('parking_status', 'closed')->count() }}
                    </h2>
                    <span class="text-muted">
                        عدد التقارير
                    </span>
                </div>
            </div>
        </div>

       
    </div>

@endsection




@section('footer')
	
	<script>
    
      	setTimeout(function() {
          location.reload();
        },30000);
      
	</script>

@endsection

