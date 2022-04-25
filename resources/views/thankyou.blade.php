@extends('layout')

@section('title', 'Thank You')

@section('extra-css')

@endsection

@section('body-class', 'sticky-footer')

@section('content')

   <div class="thank-you-section">
       <h1>謝謝您的選購 !</h1>
       <p>將會有一封確認信件即將發送給您。</p>
       <div class="spacer"></div>
       <div>
           <a href="{{ url('/') }}" class="button">返回首頁</a>
       </div>
   </div>




@endsection
