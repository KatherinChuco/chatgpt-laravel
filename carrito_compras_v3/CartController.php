namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Mostrar el carrito
    public function index()
    {
        $cart = Session::get('cart', []);
        return view('cart.index', ['cart' => $cart]);
    }

    // Agregar producto al carrito
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validar la cantidad
        $quantity = intval($request->input('quantity'));
        if ($quantity <= 0 || $quantity > $product->stock) {
            return redirect()->back()->with('error', 'Cantidad no válida.');
        }

        // Manejo de sesión segura
        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        Session::put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Producto agregado al carrito.');
    }

    // Procesar pago (simulación)
    public function checkout()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'El carrito está vacío.');
        }

        // Utilizar transacción para evitar Race Condition
        DB::transaction(function () use ($cart) {
            foreach ($cart as $id => $item) {
                $product = Product::lockForUpdate()->findOrFail($id);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto: " . $product->name);
                }
                // Actualizar stock
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // Simulación de pago
            Log::info('Pago procesado para el carrito de ' . session('user_id'), ['cart' => $cart]); // Ejemplo de log seguro
            Session::forget('cart');
        });

        return redirect()->route('cart.index')->with('success', 'Pago realizado con éxito.');
    }
}
