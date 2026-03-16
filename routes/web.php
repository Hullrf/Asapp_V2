<?php

use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\MesaPublicaController;
use App\Http\Controllers\PasarelaController;
use Illuminate\Support\Facades\Route;

// ── Raíz ────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Auth ─────────────────────────────────────────────────────────────
Route::get('/login',    [LoginController::class,    'show'])->name('login');
Route::post('/login',   [LoginController::class,    'login'])->name('login.submit');
Route::post('/logout',  [LoginController::class,    'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register',[RegisterController::class, 'register'])->name('register.submit');

// AJAX: verificar rol por email (para mostrar/ocultar selector de negocio)
Route::post('/verificar-rol', [LoginController::class, 'verificarRol'])->name('verificar.rol');

// ── Admin (protegido con middleware 'admin') ──────────────────────────
Route::middleware('admin')->prefix('panel')->name('panel.')->group(function () {

    Route::get('/', [PanelController::class, 'index'])->name('index');

    // Productos
    Route::post('/productos',          [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

    // Mesas
    Route::post('/mesas',                    [MesaController::class, 'store'])->name('mesas.store');
    Route::put('/mesas/{mesa}',              [MesaController::class, 'update'])->name('mesas.update');
    Route::delete('/mesas/{mesa}',           [MesaController::class, 'destroy'])->name('mesas.destroy');
    Route::post('/mesas/{mesa}/unir',        [MesaController::class, 'unir'])->name('mesas.unir');
    Route::post('/mesas/{mesa}/separar',     [MesaController::class, 'separar'])->name('mesas.separar');

    // Pedidos
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');

    // Categorias
    Route::post('/categorias',              [CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{categoria}',   [CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{categoria}',[CategoriaController::class, 'destroy'])->name('categorias.destroy');
});

// ── Factura (accesible por admin y por cliente vía QR) ───────────────
Route::get('/factura/{pedido}',              [FacturaController::class, 'show'])->name('factura.show');
Route::post('/factura/{pedido}/item',        [FacturaController::class, 'addItem'])->name('factura.item.add')->middleware('admin');
Route::put('/factura/{pedido}/item/{item}',  [FacturaController::class, 'updateItem'])->name('factura.item.update')->middleware('admin');
Route::delete('/factura/{pedido}/item/{item}',[FacturaController::class, 'deleteItem'])->name('factura.item.delete')->middleware('admin');
Route::post('/factura/{pedido}/reabrir',      [FacturaController::class, 'reabrir'])->name('factura.reabrir')->middleware('admin');

// ── Acceso público por QR ────────────────────────────────────────────
Route::get('/mesa/{qr}', [MesaPublicaController::class, 'show'])->name('mesa.publica');

// ── Pasarela de pago ─────────────────────────────────────────────────
Route::get('/pasarela/{pedido}',  [PasarelaController::class, 'show'])->name('pasarela.show');
Route::post('/pasarela/{pedido}', [PasarelaController::class, 'confirmar'])->name('pasarela.confirmar');
Route::get('/pago-exitoso/{pedido}', [PasarelaController::class, 'exitoso'])->name('pago.exitoso');
