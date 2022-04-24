@extends('layout')

@section('title', 'Sign Up for an Account')

@section('content')
<div class="container">
    <div class="auth-pages">
        <div>
            @if (session()->has('success_message'))
            <div class="alert alert-success">
                {{ session()->get('success_message') }}
            </div>
            @endif @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h2>建立帳號</h2>
            <div class="spacer"></div>

            <form method="POST" action="{{ route('register') }}">
                {{ csrf_field() }}

                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="姓名" required autofocus>

                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required>

                <input id="password" type="password" class="form-control" name="password" placeholder="Password" placeholder="密碼" required>

                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="確認密碼"
                    required>

                <div class="login-container">
                    <button type="submit" class="auth-button">建立帳號</button>
                    <div class="already-have-container">
                        <p><strong>你已經有帳號?</strong></p>
                        <a href="{{ route('login') }}">登入</a>
                    </div>
                </div>

            </form>
        </div>

        <div class="auth-right">
            <h2>註冊新帳號</h2>
            <div class="spacer"></div>
            <p><strong>節省時間來結帳</strong></p>
            <p>建立帳戶後，您可以更快地結賬，查詢訂單歷史記錄，並根據您的喜好定制您的體驗。</p>

        </div>
    </div> <!-- end auth-pages -->
</div>
@endsection
