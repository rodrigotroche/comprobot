@extends('frontend.layouts.app')

@section('content')
<div class="container mt-5" id="shoppingList" data-id="{{ $shoppingList->id }}">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h2>{{ $shoppingList->name }}</h2>
                <small><span class="text-muted">Creado el: {{ $shoppingList->created_at->format('d/m/Y') }}</span></small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" id="productResults">
                <div class="card-body">
                    <h5 class="card-title">Agregar Productos</h5>
                    <form id="shoppingListForm">
                        <!-- Búsqueda de productos -->
                        <div class="mb-3">
                            <!-- <label for="productSearch" class="form-label">Buscar</label> -->
                            <input type="text" id="productSearch" class="form-control" placeholder="Nombre del producto">
                        </div>

                        <div id="productsList" class="list-group">
                            @foreach($featuredProducts as $product)
                            <button type="button" class="list-group-item list-group-item-action" onclick="addProductToList('{{ $product->id }}')">
                                {{ $product->name }} <span class="badge bg-primary float-end">Añadir</span>
                            </button>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Lista de productos añadidos -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Productos en la lista</h5>
                    <ul id="shoppingListItems" class="list-group">
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- total y detalles -->

    <div class="row mt-4">
        <div class="col-md-4">
            <p>Total: <span id="total">0</span></p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@php
$routes = [
"show" => route("frontend.shopping-lists.show", $shoppingList),
"shippingListItemStore" => route("frontend.shopping-list-items.store"),
"shippingListItemDestroy" => route("frontend.shopping-list-items.destroy", ":id"),
];
@endphp

<script>
    const routes = @json($routes);

    const shoppingListId = document.getElementById('shoppingList').dataset.id;
    const shoppingListRoute = document.getElementById('shoppingList').dataset.route;

    // Aquí se simula la búsqueda y la adición de productos a la lista de compras
    function addProductToList(productId) {
        const shippingListId = document.getElementById('shoppingList').dataset.id;
        requestStoreProductToList({
                shoppingListId,
                productId
            })
            .then(responseData => {
                getShoppingList();
            })
            .catch(error => {
                handleProductAdditionError(error); // Maneja el error si ocurre
            });
    }

    handleProductAdditionError = (error) => {
        console.error(error);
    }

    function renderProductsListItems(shoppingList) {
        const productsList = document.getElementById('shoppingListItems');
        productsList.innerHTML = '';

        if (shoppingList.products.length === 0) {
            productsList.innerHTML = '<li class="list-group-item">No hay productos en la lista</li>';
            return;
        }

        shoppingList.products.forEach(product => {
            const listItem = document.createElement('div');
            listItem.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';

            // Crear el nombre del producto y el input para la cantidad
            listItem.innerHTML = `
            <span>${product.name}</span>
            <input 
                type="number" 
                class="form-control w-25 me-2" 
                value="${product.quantity}" 
                min="1" 
                onchange="updateProductQuantity('${product.pivot.id}', this.value)"
            />
            <span class="badge bg-danger" onclick="removeProductFromList('${product.pivot.id}')">Eliminar</span>
        `;

            productsList.appendChild(listItem);
        });
    }

    function removeProductFromList(productId) {
        requestRemoveProductFromList(productId)
            .then(responseData => {
                getShoppingList();
            })
            .catch(error => {
                handleProductRemovalError(error); // Maneja el error si ocurre
            });
    }

    function requestStoreProductToList(data) {
        const {
            shippingListId,
            productId
        } = data;

        console.log(data, routes.shippingListItemStore);

        return fetch(routes.shippingListItemStore, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    shopping_list_id: shoppingListId,
                    product_id: productId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error adding product: ${response.statusText}`);
                }
                return response.json();
            });
    }

    // Función que realiza la solicitud HTTP para eliminar el producto
    function requestRemoveProductFromList(productId) {
        const route = routes.shippingListItemDestroy.replace(':id', productId);
        return fetch(route, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error removing product: ${response.statusText}`);
                }
                return response.json();
            });
    }

    function getShoppingList() {
        const shoppingListRoute = routes.show;
        fetch(shoppingListRoute, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                renderProductsListItems(data);
            });
    }

    getShoppingList();


    // Aquí puedes agregar código para hacer la búsqueda de productos con AJAX y mostrarlos dinámicamente
</script>

@endpush