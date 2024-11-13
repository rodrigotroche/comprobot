@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <div class="col">
            <div class="border rounded-3 p-5" style="background-color: #f8f9fa;">
                <img src="{{ asset('images/' . $product->storeProducts->first()->image) }}"
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
                    <small class="text-muted">SKU: {{ $product->sku }}</small>
                </p>

                <table class="table">
                    <tbody>
                        @foreach($product->storeProducts as $storeProduct)
                        <tr>
                            <td>{{ $storeProduct->store->name }}</td>
                            <td>
                                {{ $storeProduct->formatted_price }}
                                @if ($storeProduct->hasDiscount())
                                <span class="text-decoration-line-through">{{ $storeProduct->formatted_previous_price }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<div class="container mt-3">
    <div class="col-md-12">
        <h3>Historico de precios</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tienda</th>
                        <th>Fecha</th>
                        <th>Precio</th>
                        <th>Descuento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->storeProducts as $storeProduct)
                    <tr>
                        <td colspan="4">
                            <strong>{{ $storeProduct->store->name ?? 'Sin nombre de tienda' }}</strong>
                        </td>
                    </tr>
                    @foreach($product->priceHistories as $priceHistory)
                    <tr>
                        <td></td>
                        <td>{{ $priceHistory->created_at->format('d-m-Y') }}</td>
                        <td>{{ $priceHistory->formatted_price }}</td>
                        <td>{{ $priceHistory->formatted_previous_price }}</td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection