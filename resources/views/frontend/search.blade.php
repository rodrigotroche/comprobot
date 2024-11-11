@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="py-5 text-center">
        <!-- <img class="d-block mx-auto mb-4" src="https://getbootstrap.com/docs/5.1/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57"> -->
        <h2>Buscador de productos</h2>
        <p class="lead">Buscá productos en nuestra base de datos</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('frontend.search') }}" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="search" placeholder="Buscar productos" autocomplete="off" aria-label="Buscar productos" aria-describedby="button-addon2" value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Buscar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <div class="py-4">
                <h3>Resultados de la búsqueda</h3>
            </div>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4 row-cols-sm-2 row-cols-lg-4 row-cols-xl-5">
        @foreach ($products as $product)
        <div class="col">
            <div class="card h-100 shadow-sm product-item" id="product-{{ $product->id }}">
                <div class="card-body text-center">
                    <a href="{{ route('frontend.products.show', $product) }}" class="product-item--link">
                        @if ($product->storeProducts->count() > 0)
                        <img src="{{ asset('images/' . $product->storeProducts->last()->image) }}"
                            class="card-img-top"
                            alt="{{ $product->name }}"
                            style="margin: 0 auto 20px auto; display:block; max-height: 150px; width:auto; max-width: 100%;">
                        @endif
                        <h6 class="card-title">{{ $product->name }}</h6>
                        @if ($product->storeProducts->count() > 0)
                        <div class="product-item--price-container">
                            <span class="d-block product-item--price">
                                {{ $product->storeProducts->last()->formatted_price }}
                            </span>
                            @if ($product->storeProducts->last()->hasDiscount())
                            <span class="product-item--price-discount text-decoration-line-through text-muted">{{ $product->storeProducts->first()->formatted_previous_price }}</span>
                            @endif
                        </div>
                        <div class="product-item--sku">
                            <span class="text-muted">SKU: {{ $product->storeProducts->last()->sku }}</span>
                        </div>
                        @endif
                    </a>

                    <div class="product-item--action">
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle dropnone" type="button" id="dropdownMenuButton-{{ $product->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $product->id }}">
                                <a class="dropdown-item" href="#">
                                    Agregar a lista
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection