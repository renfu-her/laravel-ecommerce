@extends('layout')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="auth-pages">
        <div class="auth-left">
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
            <h2>登入</h2>
            <div class="spacer"></div>

            <form action="{{ route('login') }}" method="POST">
                {{ csrf_field() }}

                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                <input type="password" id="password" name="password" value="{{ old('password') }}" placeholder="密碼" required>

                <div class="login-container">
                    <button type="submit" class="auth-button">登入</button>
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> 記住我
                    </label>
                </div>

                <div class="spacer"></div>

                <a href="{{ route('password.request') }}">
                    忘記密碼?
                </a>

            </form>
        </div>

        <div class="auth-right">
            <h2>註冊新帳號</h2>
            <div class="spacer"></div>
            <p><strong>節省時間來結帳</strong></p>
            <p>不需要帳號就可以結帳.</p>
            <div class="spacer"></div>
            <a href="{{ route('guestCheckout.index') }}" class="auth-button-hollow">不需要帳號</a>
            <div class="spacer"></div>
            &nbsp;
            <div class="spacer"></div>
            <p><strong>稍微等待一下</strong></p>
            <p>建立帳號以快速結帳，並且可以查詢訂單歷史記錄</p>
            <div class="spacer"></div>
            <a href="{{ route('register') }}" class="auth-button-hollow">建立帳號</a>

        </div>
    </div>
</div>
@endsection
