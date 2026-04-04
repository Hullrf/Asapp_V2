<?php

use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\PisoController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\MesaPublicaController;
use App\Http\Controllers\PasarelaController;
use Illuminate\Support\Facades\Route;

// ── Raíz ────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Superadmin ───────────────────────────────────────────────────────
Route::get('/superadmin/login',  [SuperAdminController::class, 'showLogin'])->name('superadmin.login');
Route::post('/superadmin/login', [SuperAdminController::class, 'login'])->name('superadmin.login.submit');
Route::post('/superadmin/logout',[SuperAdminController::class, 'logout'])->name('superadmin.logout');

Route::middleware('superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/',                          [SuperAdminController::class, 'panel'])->name('panel');
    Route::put('/negocios/{negocio}',        [SuperAdminController::class, 'update'])->name('negocios.update');
    Route::delete('/negocios/{negocio}',     [SuperAdminController::class, 'destroy'])->name('negocios.destroy');
});

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

    // Parciales AJAX
    Route::get('/partials/inventario',   [PanelController::class, 'parcialInventario'])->name('partials.inventario');
    Route::get('/partials/mesas',        [PanelController::class, 'parcialMesas'])->name('partials.mesas');
    Route::get('/partials/nuevo-pedido', [PanelController::class, 'parcialNuevoPedido'])->name('partials.nuevo-pedido');
    Route::get('/partials/estadisticas', [PanelController::class, 'parcialEstadisticas'])->name('partials.estadisticas');
    Route::get('/partials/historial',    [PanelController::class, 'parcialHistorial'])->name('partials.historial');

    // Productos
    Route::post('/productos',          [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

    // Sedes
    Route::post('/sedes',                [SedeController::class, 'store'])->name('sedes.store');
    Route::post('/sedes/{negocio}/activar', [SedeController::class, 'activar'])->name('sedes.activar');

    // Pisos
    Route::post('/pisos',              [PisoController::class, 'store'])->name('pisos.store');
    Route::put('/pisos/{piso}',        [PisoController::class, 'update'])->name('pisos.update');
    Route::delete('/pisos/{piso}',     [PisoController::class, 'destroy'])->name('pisos.destroy');

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
Route::get('/factura/{pedido}/sync',         [FacturaController::class, 'sync'])->name('factura.sync');
Route::post('/factura/{pedido}/item',        [FacturaController::class, 'addItem'])->name('factura.item.add')->middleware('admin');
Route::put('/factura/{pedido}/item/{item}',  [FacturaController::class, 'updateItem'])->name('factura.item.update')->middleware('admin');
Route::delete('/factura/{pedido}/item/{item}',[FacturaController::class, 'deleteItem'])->name('factura.item.delete')->middleware('admin');
Route::post('/factura/{pedido}/reabrir',      [FacturaController::class, 'reabrir'])->name('factura.reabrir')->middleware('admin');

// División de ítems
Route::post('/factura/{pedido}/item/{item}/dividir',          [DivisionController::class, 'iniciar'])->name('division.iniciar');
Route::post('/factura/{pedido}/division/{division}/tomar',    [DivisionController::class, 'tomar'])->name('division.tomar');
Route::post('/factura/{pedido}/division/{division}/liberar',  [DivisionController::class, 'liberar'])->name('division.liberar');
Route::post('/factura/{pedido}/division/{division}/cancelar', [DivisionController::class, 'cancelar'])->name('division.cancelar');
Route::patch('/factura/{pedido}/division/{division}',         [DivisionController::class, 'actualizar'])->name('division.actualizar');
Route::post('/factura/{pedido}/division/{division}/extraer',  [DivisionController::class, 'extraerParte'])->name('division.extraer');

// ── Acceso público por QR ────────────────────────────────────────────
Route::get('/mesa/{qr}', [MesaPublicaController::class, 'show'])->name('mesa.publica');

// ── Pasarela de pago ─────────────────────────────────────────────────
Route::get('/pasarela/{pedido}',  [PasarelaController::class, 'show'])->name('pasarela.show');
Route::post('/pasarela/{pedido}', [PasarelaController::class, 'confirmar'])->name('pasarela.confirmar');
Route::get('/pago-exitoso/{pedido}',  [PasarelaController::class, 'exitoso'])->name('pago.exitoso');
Route::get('/pago-fallido/{pedido}',  [PasarelaController::class, 'fallido'])->name('pasarela.fallido');
Route::post('/factura/{pedido}/cobrar-efectivo', [PasarelaController::class, 'cobrarEfectivo'])
    ->name('pago.efectivo')
    ->middleware('admin');
