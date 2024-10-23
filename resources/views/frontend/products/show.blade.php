@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <div class="col">
            <div class="border rounded-3 p-5" style="background-color: #f8f9fa;">
                <img src="{{ asset('images/' . $product->storeProducts->last()->image) }}"
                    class="card-img-top rounded"
                    alt="{{ $product->name }}"
                    style="max-width: 100%;">
            </div>
        </div>
        <div class="col">
            <div class="py-5">
                <h2>{{ $product->name }}</h2>
                <p>{{ $product->description }}</p>
                <p>

                    Precio: {{ $product->storeProducts->last()->formatted_price }}
                    @if ($product->storeProducts->last()->hasDiscount())
                    <span class="text-decoration-line-through">{{ $product->storeProducts->first()->formatted_previous_price }}</span>
                    @endif

                </p>
                <p>
                    <small class="text-muted">SKU: {{ $product->storeProducts->last()->sku }}</small>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="col-md-12">
        <h3>Precios en otras tiendas</h3>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3" style="background-color: #f8f9fa;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="https://www.superseis.com.py/App_Themes/Stock/images/logo.png" class="img-fluid rounded-start" alt="superseis">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">Superseis</h5>
                            <p class="card-text">
                                Precio: $100
                                <br>
                                SKU: 123456
                            </p>
                            <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3" style="background-color: #f8f9fa;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="https://www.stock.com.py/App_Themes/Stock/images/logo.png" class="img-fluid rounded-start" alt="superseis">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">Stock</h5>
                            <p class="card-text">
                                Precio: $100
                                <br>
                                SKU: 123456
                            </p>
                            <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3" style="background-color: #f8f9fa;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="https://biggie.com.py/_ipx/w_175/img/logo-biggie.svg" class="img-fluid rounded-start" alt="superseis">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">Biggie</h5>
                            <p class="card-text">
                                Precio: $100
                                <br>
                                SKU: 123456
                            </p>
                            <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection