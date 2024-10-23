@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="py-5 text-center">
        <!-- <img class="d-block mx-auto mb-4" src="https://getbootstrap.com/docs/5.1/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57"> -->
        <h2>Bienvenido al buscador de productos</h2>
        <p class="lead">Encuentra los productos que necesitas al mejor precio.</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('frontend.search') }}" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="search" placeholder="Buscar productos" aria-label="Buscar productos" aria-describedby="button-addon2">
                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Buscar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection