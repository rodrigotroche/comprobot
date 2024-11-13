<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ShoppingListItemController;
use App\Http\Controllers\SuperseisController;

Route::get('/', [MainController::class, 'index'])->name('frontend.index');
Route::get('/s', [MainController::class, 'search'])->name('frontend.search');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('frontend.products.show');

Route::get('/lists', [ShoppingListController::class, 'index'])->name('frontend.shopping-lists.index');
Route::get('/lists/create', [ShoppingListController::class, 'create'])->name('frontend.shopping-lists.create');
Route::post('/lists', [ShoppingListController::class, 'store'])->name('frontend.shopping-lists.store');
Route::get('/lists/{shoppingList}', [ShoppingListController::class, 'show'])->name('frontend.shopping-lists.show');
Route::get('/lists/{shoppingList}/edit', [ShoppingListController::class, 'edit'])->name('frontend.shopping-lists.edit');
Route::patch('/lists/{shoppingList}', [ShoppingListController::class, 'update'])->name('frontend.shopping-lists.update');
Route::delete('/lists/{shoppingList}', [ShoppingListController::class, 'destroy'])->name('frontend.shopping-lists.destroy');

Route::post('/list-items', [ShoppingListItemController::class, 'store'])->name('frontend.shopping-list-items.store');
Route::delete('/list-items/{shoppingListItem}', [ShoppingListItemController::class, 'destroy'])->name('frontend.shopping-list-items.destroy');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/superseis/categories', [SuperseisController::class, 'getCategories'])->name('superseis.categories');

require __DIR__ . '/auth.php';
