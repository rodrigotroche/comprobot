@extends('frontend.layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Mis Listas de Compras</h2>

    <!-- Botón para crear una nueva lista -->
    <div class="text-center mb-4">
        <a href="{{ route('frontend.shopping-lists.create') }}" class="btn btn-primary">Crear Nueva Lista</a>
    </div>

    <div class="row">
        @foreach($shoppingLists as $shoppingList)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $shoppingList->name }}</h5>
                    <p class="card-text">Creado el: {{ $shoppingList->created_at->format('d/m/Y') }}</p>
                    <p class="card-text">Productos: {{ $shoppingList->items->count() }}</p>
                    <a href="{{ route('frontend.shopping-lists.edit', $shoppingList) }}" class="btn btn-outline-primary w-100">Ver Detalles</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginación -->

    <div class="mt-4">
        {{ $shoppingLists->links() }}
    </div>
</div>


@endsection