@extends('assistant.layouts.master')

@section('title',trans('home.home'))

@section('content')

<div class="row">

    <!-- Website Analytics-->
    <div class="col-lg-12 col-md-12 mb-4">

        <h3>
            مرحبا بك يا  {{ auth()->guard('assistant')->user()->name }}
        </h3>

    </div>

  </div>
@endsection
