@extends('layout')

@section('title', '搜尋結果')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
@endsection

@section('content')

    @component('components.breadcrumbs')
        <a href="/">首頁</a>
        <i class="fa fa-chevron-right breadcrumb-separator"></i>
        <span>搜尋</span>
    @endcomponent

    <div class="container">
        @if (session()->has('success_message'))
            <div class="alert alert-success">
                {{ session()->get('success_message') }}
            </div>
        @endif

        @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="search-results-container container">
        <h1>搜尋結果</h1>
        <p class="search-results-count">搜尋 '{{ request()->input('query') }}' 總共 {{ $products->total() }} 筆資料 </p>

        @if ($products->total() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>名稱</th>
                    <th style="width: 30%;">詳細資料</th>
                    <th style="width: 30%">特色說明</th>
                    <th>價錢</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <th><a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a></th>
                        <td>{{ $product->details }}</td>
                        <td>{!! str_limit($product->description, 80) !!} </td>
                        <td>{!! $product->presentPrice() !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $products->appends(request()->input())->links() }}
        @endif
    </div> <!-- end search-results-container -->

@endsection

@section('extra-js')
    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>
@endsection
