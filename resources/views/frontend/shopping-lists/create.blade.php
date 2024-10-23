@extends('frontend.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center">Crear tu Lista de Compras</h2>
            <form id="shoppingListForm" action="{{ route('frontend.shopping-lists.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" id="name" class="form-control" placeholder="Nombre del producto" name="name">

                    @error('name')
                    <div class="alert alert-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Botón para guardar la lista -->
                <button type="submit" class="btn btn-primary mt-3">Guardar lista y continuar</button>
            </form>
        </div>
    </div>
</div>
@endsection