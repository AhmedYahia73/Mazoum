@extends('admin.layouts.master')
{{-- title --}}

@section('title','تفاصيل الحدث')

@section('header')

  <style>
    th , td {
      text-align: center !important
    }

    .is_popularity i {
        color: blue
    }
  </style>

@endsection



@section('content')




<!-- Zero configuration table -->
<section id="basic-datatable">

  @include('flash-message')

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            تفاصيل الحدث
          </h4>
        </div>
        <div class="card-body card-dashboard">

          <div class="table-responsive">

            <table class="table zero-configuration">

              <thead>
                <tr>
                    <th>#</th>
                    <th> الوصف  </th>
                  	<th> ملاحظات  </th>
                    <th> التاريخ  </th>
                </tr>
              </thead>

              <tbody>

                @php $x = 1; @endphp

                @foreach($logs as $value)
                                
                	@php
                		$log = json_decode($value->log,true);
                	@endphp
                


                  <tr>
                      <td> {{ $x }} </td>
                    
                      <td>
                    	@if( is_array($log) && array_key_exists("entry",$log) && array_key_exists("changes",$log['entry'][0]) && array_key_exists("value",$log['entry'][0]['changes'][0]) && array_key_exists("statuses",$log['entry'][0]['changes'][0]['value']) && array_key_exists("status",$log['entry'][0]['changes'][0]['value']['statuses'][0])  )
                         
                        	حالة الحدث <b>  {{ $log['entry'][0]['changes'][0]['value']['statuses'][0]['status'] }}  </b>
                                            	
                        @elseif( is_array($log) && array_key_exists("entry",$log) && array_key_exists("changes",$log['entry'][0]) && array_key_exists("value",$log['entry'][0]['changes'][0]) && array_key_exists("messages",$log['entry'][0]['changes'][0]['value']) && array_key_exists("button",$log['entry'][0]['changes'][0]['value']['messages'][0]) )

							 تم <b> ( {{ $log['entry'][0]['changes'][0]['value']['messages'][0]['button']['text'] }} ) </b>
                        
                        @else
							{{ $value->log }}
                        @endif
                      </td>
                    
                      @php

                          $error_title = $value->error_title;
                          $error_details = $value->error_details;
                    
                    	  if(is_array($log) && 
                    		array_key_exists("entry",$log) && array_key_exists("changes",$log['entry'][0]) && array_key_exists("value",$log['entry'][0]['changes'][0]) && array_key_exists("statuses",$log['entry'][0]['changes'][0]['value']) && array_key_exists("errors",$log['entry'][0]['changes'][0]['value']['statuses'][0]) && 
                            array_key_exists("title", $log['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) && array_key_exists("error_data", $log['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) ) {

                              $error_title = $log['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['title'];
                              $error_details = $log['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['error_data']['details'];

                          }
                      @endphp
                    
                    
                      <td>
                        <b style="color:red"> {{ $error_title != null ? $error_title . ' - ' : '' }}  </b> 
                        <span style="color:blue"> {{ $error_details != null ? $error_details : '' }} </span>
                      </td>
                    
                      <td style="direction: ltr;"> {{ Carbon\Carbon::parse($value->created_at)->format('Y-m-d h:i A') }} </td>
         
                  </tr>

                  @php $x = $x + 1; @endphp

                @endforeach


              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!--/ Zero configuration table -->



@endsection

