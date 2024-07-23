

@extends('layouts.frontend.app')

@section('content')
  <section class="client-sec">
      <div class="container">
        <div class="section-title">
          <h2>{{ ucfirst($cms->name)}}</h2>
          <!-- <div class="bar"></div> -->
        </div>
        <div class="retailer-slider">
            <div class="content">{!! $cms->content !!} </div>
        </div>
      </div>
    </section>
 @endsection 