@extends('layout')

@section('title', '結帳')

@section('extra-css')
    <style>
        .mt-32 {
            margin-top: 32px;
        }
    </style>

{{--    <script src="https://js.stripe.com/v3/"></script>--}}

@endsection

@section('content')

    <div class="container">

        @if (session()->has('success_message'))
            <div class="spacer"></div>
            <div class="alert alert-success">
                {{ session()->get('success_message') }}
            </div>
        @endif

        @if(count($errors) > 0)
            <div class="spacer"></div>
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1 class="checkout-heading stylish-heading">結帳</h1>
        <div class="checkout-section">
            <div>
{{--                <form action="{{ route('checkout.store') }}" method="POST" id="payment-form">--}}
                <form action="/newbpay" method="POST" id="payment-form">
                    {{ csrf_field() }}
                    <h2>付款明細</h2>

                    <div class="form-group">
                        <label for="email">你的信箱</label>
                        @if (auth()->user())
                            <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" readonly>
                        @else
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="name">名稱</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="half-form">
                        <div class="form-group">
                            <label for="city">國家</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="province">縣市</label>
                            <input type="text" class="form-control" id="province" name="province" value="{{ old('province') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">地址</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required>
                    </div>

                     <!-- end half-form -->

                    <div class="half-form">
                        <div class="form-group">
                            <label for="postalcode">郵政編號</label>
                            <input type="text" class="form-control" id="postalcode" name="postalcode" value="{{ old('postalcode') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">電話</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>
                    </div> <!-- end half-form -->

                    <div class="spacer"></div>
                    <h2>付款方式</h2>

                    <div class="form-check form-check-inline">

                            <input type="radio" class="form-check-input" name="charge_type[]" value="CREDIT" checked>
                            <label class="form-check-label">信用卡</label>

                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="charge_type[]" value="WEBATM">
                        <label for="name_on_card" class="form-check-label">WebATM</label>
                    </div>
                    <input type="hidden" id="price" name="price" value="{{ presentPrice(Cart::subtotal()) }}">
                    <button type="submit" id="complete-order" class="button-primary full-width">確認購買</button>


                </form>

                @if ($paypalToken)
                    <div class="mt-32">or</div>
                    <div class="mt-32">
                        <h2>Pay with PayPal</h2>

                        <form method="post" id="paypal-payment-form" action="{{ route('checkout.paypal') }}">
                            @csrf
                            <section>
                                <div class="bt-drop-in-wrapper">
                                    <div id="bt-dropin"></div>
                                </div>
                            </section>

                            <input id="nonce" name="payment_method_nonce" type="hidden" />
                            <button class="button-primary" type="submit"><span>Pay with PayPal</span></button>
                        </form>
                    </div>
                @endif
            </div>



            <div class="checkout-table-container">
                <h2>你的訂單</h2>

                <div class="checkout-table">
                    @foreach (Cart::content() as $item)
                    <div class="checkout-table-row">
                        <div class="checkout-table-row-left">
                            <img src="{{ productImage($item->model->image) }}" alt="item" class="checkout-table-img">
                            <div class="checkout-item-details">
                                <div class="checkout-table-item">{{ $item->model->name }}</div>
                                <div class="checkout-table-description">{{ $item->model->details }}</div>
                                <div class="checkout-table-price">{!! $item->model->presentPrice() !!}</div>
                            </div>
                        </div> <!-- end checkout-table -->

                        <div class="checkout-table-row-right">
                            <div class="checkout-table-quantity">{{ $item->qty }}</div>
                        </div>
                    </div> <!-- end checkout-table-row -->
                    @endforeach

                </div> <!-- end checkout-table -->

                <div class="checkout-totals">
                    <div class="checkout-totals-left">
                        價格 <br>
                        @if (session()->has('coupon'))
                            Discount ({{ session()->get('coupon')['name'] }}) :
                            <br>
                            <hr>
                            New Subtotal <br>
                        @endif
{{--                        含稅 ({{config('cart.tax')}}%)<br>--}}
                        <span class="checkout-totals-total">總價</span>

                    </div>

                    <div class="checkout-totals-right">
                        {!! presentPrice(Cart::subtotal()) !!} <br>
                        @if (session()->has('coupon'))
                            -{{ presentPrice($discount) }} <br>
                            <hr>
                            {{ presentPrice($newSubtotal) }} <br>
                        @endif
{{--                        {{ presentPrice($newTax) }} <br>--}}
                        <span class="checkout-totals-total">{!! presentPrice($newTotal) !!}</span>

                    </div>
                </div> <!-- end checkout-totals -->
            </div>

        </div> <!-- end checkout-section -->
    </div>

@endsection

@section('extra-js')
    <script src="https://js.braintreegateway.com/web/dropin/1.13.0/js/dropin.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function(){

            const price = $('#price').val()
            const price_regex = /(<([^>]+)>)/ig
            let result = price.replace(price_regex, "");
            result = result.replace('網路價: ', "")
            result = result.replace(',', "")
            $('#price').val(result);

        });
    </script>
@endsection
