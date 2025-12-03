
<!--begin::Form-->
{!! Form::open(['url' => "admin/events/update_location", 'role'=>'form','id'=>'update_location','method'=>'post']) !!}


    <input type="hidden" name="id" value="{{ $Item->id }}" required>
    <input type="hidden" name="lat" id="lat2" value="{{ $Item->lat }}">
    <input type="hidden" name="long" id="lon2" value="{{ $Item->long }}">
    <input type="hidden" name="country" id="country2" value="{{ $Item->country }}">
    <input type="hidden" name="location" id="location2" value="{{ $Item->location }}">

   <div class="row">

        <div class="col-lg-12">
            <div class="div-center">
                <br>

                <!-- Search input -->
                <input id="searchInput" class="controls" type="text" placeholder="أبحث عن العنوان">
                <br>
                <br>
       		    <!-- Google map -->
                <div id="map"></div>
                <br>
                <!-- Display geo location data -->
                <ul class="geo-data">
                    <li style="display: block"> الدوله : <span id="country">{{ $Item->country }}</span></li>
                    <li style="display: block"> العنوان بالكامل : <span id="location">{{ $Item->location }}</span></li>
                    <li style="display: block"> خطوط العرض : <span id="lat">{{ $Item->latitude }}</span></li>
                    <li style="display: block"> دوائر الطول : <span id="lon">{{ $Item->longitude }}</span></li>
                </ul>

            </div>
        </div>

       <br>

        <div class="col-md-12">
           <button type="submit" form="update_location" class="btn btn-primary mr-2">
               {{ trans('home.update') }}
            </button>
        </div>

   </div>

{!! Form::close() !!}
<!--end::Form-->
