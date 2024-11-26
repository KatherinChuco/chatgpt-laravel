use App\Http\Controllers\CarritoController;

Route::get('/', [CarritoController::class, 'index']);
Route::get('carrito', [CarritoController::class, 'verCarrito'])->name('carrito.ver');
Route::get('carrito/agregar/{id}', [CarritoController::class, 'agregar']);
Route::get('carrito/eliminar/{id}', [CarritoController::class, 'eliminarProducto']);
Route::get('carrito/procesar', [CarritoController::class, 'procesarPago'])->name('carrito.procesar');
Route::get('carrito/success', [CarritoController::class, 'pagoExitoso'])->name('carrito.success');
Route::get('carrito/cancel', [CarritoController::class, 'pagoCancelado'])->name('carrito.cancel');
