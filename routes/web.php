<?php

use App\Filament\Pages\Calidad;
use App\Filament\Pages\CrearMezcla;
use App\Filament\Pages\FabricacionesPage;
use App\Filament\Pages\Profile;
use Illuminate\Support\Facades\Route;
use App\Services\PdfService;
use App\Http\Controllers\MescladoController;
use App\Http\Controllers\MescladoInsController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProduccionFinalController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\CalculoMezclasController;
use App\Models\Insumos;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/nosotros', function () {
    return view('nosotros');
});
Route::get('/login', function () {
    return redirect()->route('filament.Dashboard.auth.login');
});
Route::get('/ubicacion', function () {
    return view('ubicacion');
});
Route::get('/generar-pdf', function (PdfService $pdfService) {
    $pedido = \App\Models\Pedido::first();
    return $pdfService->generarPedido($pedido);
});
Route::get('/profile', Profile::class)->name('filament.Dashboard.auth.profile');

Route::get('fabricaciones', FabricacionesPage::class);
Route::get('calidad', Calidad::class);


Route::get('/Dashboard/logout', function () {
    Auth::logout();
    return redirect('/Dashboard');
})->name('logout');

Route::get('/pedidos/{pedido}/cotizacion', [PedidoController::class, 'generarCotizacion'])->name('generar.cotizacion');
Route::get('/pedidos/{pedido}/pedido', [PedidoController::class, 'generarPedido'])->name('generar.pedido');

Route::post('/calcular-mezclas', [CalculoMezclasController::class, 'calcularCantidadMezclas']);