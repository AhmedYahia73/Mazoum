@extends('assistant.layouts.master')
{{-- title --}}

@section('title',$title)

@section('header')


@endsection


@section('content')

    <!-- Cards with badge -->
    <div class="row justify-content-center" style="margin-top: 20px">

        <div class="col-12">
            @include('flash-message')
        </div>

        <div class="col-12">
            <div class="card"  style="padding: 20px">

                <h3>
                    {{ $title }}
                </h3>

                <div>

                    <div class="row justify-content-center">

                        @if($messages != null && $messages->count() > 0)
                          @foreach($messages as $message)
                          <div class="col-md-12" style="margin-bottom:20px;background: #fbf8f8;padding-left: 0;padding-right: 0;position:relative">
                              <div class="card-header" style="color:#000;margin-bottom: 10px;">
                                  {{ $message->name }}
                              </div>
                            <div class="card-body">
                              <h5 class="card-title" style="color:#000;margin-bottom: 5px !important;">
                                  {{ $message->mobile }}
                              </h5>
                              <p class="card-text" style="color:#000">
                                  {{ $message->message }}
                              </p>
                            </div>
                            	
                            <a onclick="return DeletingModal({{ $message->id }});" class="DeletingModal" name="{{ $message->id }}" href="javascript:void(0);" style="position:absolute;left: 20px;top: 20px;">
                                <i class="bx bx-trash me-1"></i> {{ trans('home.Delete') }}
                            </a>
                          </div>
                          @endforeach
                        @else
                            <div class="col-md-12" style="margin-bottom:20px;">
                            <div class="card-body">
                              <p class="card-text" style="text-align: center;color: #FFF;font-size: 55px;margin-top: 150px;">
                                  عفوا لا يوجد اي رسائل تهنئه
                              </p>
                            </div>
                          </div>
                        @endif

                      </div>

                </div>

            </div>
        </div>

    </div>

@endsection



{{-- vendor scripts --}}
@section('footer')


    <script>

        function DeletingModal(ID) {
            swal({
                title: "{{ trans('home.delete_msg1') }}",
                text: "{{ trans('home.delete_msg2') }}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{ trans('home.yes') }}",
                cancelButtonText: "{{ trans('home.no') }}",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    window.location.href = '{{ url('assistant_panel/delete-event-messages') }}' + '/' + ID + '/' + '{{$type}}';

                }
            });
        }

    </script>


@endsection



