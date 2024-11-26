namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Mostrar productos y el carrito
    public function index()
    {
        $products = Product::all();
        $cart = Session::get('cart', []);
        $total = array_reduce($cart, function ($sum, $item) {
            return $sum + $item['price'] * $item['quantity'];
        }, 0);

        return view('cart.index', compact('products', 'cart', 'total'));
    }

    // Agregar producto al carrito
    public function add(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $cart = Session::get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity ?? 1;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity ?? 1,
            ];
        }

        Session::put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Producto agregado al carrito.');
    }

    // Eliminar producto del carrito
    public function remove($id)
    {
        $cart = Session::get('cart', []);
        unset($cart[$id]);
        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Producto eliminado del carrito.');
    }

    // Vaciar el carrito
    public function clear()
    {
        Session::forget('cart');
        return redirect()->route('cart.index')->with('success', 'Carrito vaciado.');
    }

    // Simular pago
    public function checkout()
    {
        Session::forget('cart');
        return redirect()->route('cart.index')->with('success', '¡Pago realizado con éxito!');
    }
}
