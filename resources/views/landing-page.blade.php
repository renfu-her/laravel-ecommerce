<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>GC 3C 賣場</title>
        <link rel="icon" href="/storage/{{ setting('site.logo') }}">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat%7CRoboto:300,400,700" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    </head>
    <body>
        <div id="app">
            <header class="with-background">
                <div class="top-nav container">
                    <div class="top-nav-left">
                        <div class="logo">3C 賣場</div>
                        {{ menu('main', 'partials.menus.main') }}
                    </div>
                    <div class="top-nav-right">
                        @include('partials.menus.main-right')
                    </div>
                </div> <!-- end top-nav -->
                <div class="hero container">
                    <div class="hero-copy">
                        <h1>3C 大賣場</h1>
                        <p>包含筆電、桌上型以及平板等東西</p>
{{--                        <div class="hero-buttons">--}}
{{--                            <a href="https://www.youtube.com/playlist?list=PLEhEHUEU3x5oPTli631ZX9cxl6cU_sDaR" class="button button-white">Screencasts</a>--}}
{{--                            <a href="https://github.com/drehimself/laravel-ecommerce-example" class="button button-white">GitHub</a>--}}
{{--                        </div>--}}
                    </div> <!-- end hero-copy -->

                    <div class="hero-image">
                        <img src="img/macbook-pro-laravel.png" alt="hero image">
                    </div> <!-- end hero-image -->
                </div> <!-- end hero -->
            </header>

            <div class="featured-section">

                <div class="container">
                    <h1 class="text-center">歡迎進入我們的賣場</h1>

                    <p class="section-description text-center">以下是我們的特色產品，有筆電、平板等，歡迎隨時參觀</p>

                    <div class="text-center button-container">
                        <a href="javascript:void(0)" class="button">特色商品</a>
                    </div>

                    {{-- <div class="tabs">
                        <div class="tab">
                            Featured
                        </div>
                        <div class="tab">
                            On Sale
                        </div>
                    </div> --}}

                    <div class="products text-center">
                        @foreach ($products as $product)
                            <div class="product">
                                <a href="{{ route('shop.show', $product->slug) }}"><img src="{{ productImage($product->image) }}" alt="product"></a>
                                <a href="{{ route('shop.show', $product->slug) }}"><div class="product-name">{{ $product->name }}</div></a>
                                <div class="product-price">{!! $product->presentPrice() !!}</div>
                            </div>
                        @endforeach

                    </div> <!-- end products -->

                    <div class="text-center button-container">
                        <a href="{{ route('shop.index') }}" class="button">瀏覽更多的商品</a>
                    </div>

                </div> <!-- end container -->

            </div> <!-- end featured-section -->

{{--            <blog-posts></blog-posts>--}}

            @include('partials.footer')

        </div> <!-- end #app -->
        <script src="js/app.js"></script>
    </body>
</html>
