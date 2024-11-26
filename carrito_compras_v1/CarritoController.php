// app/Http/Controllers/CarritoController.php
namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CarritoController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        return view('carrito.index', compact('productos'));
    }

    public function agregar($id)
    {
        $producto = Producto::findOrFail($id);

        $carrito = Session::get('carrito', []);
        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
        } else {
            $carrito[$id] = [
                'producto' => $producto,
                'cantidad' => 1
            ];
        }

        Session::put('carrito', $carrito);

        return redirect()->route('carrito.index');
    }

    public function verCarrito()
    {
        $carrito = Session::get('carrito', []);
        return view('carrito.ver', compact('carrito'));
    }

    public function eliminarProducto($id)
    {
        $carrito = Session::get('carrito', []);
        unset($carrito[$id]);
        Session::put('carrito', $carrito);

        return redirect()->route('carrito.ver');
    }

    public function procesarPago()
    {
        $carrito = Session::get('carrito', []);
        $total = 0;

        foreach ($carrito as $item) {
            $total += $item['producto']->precio * $item['cantidad'];
        }

        Stripe::setApiKey('tu_clave_secreta_de_stripe');

        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => array_map(function($item) {
                return [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item['producto']->nombre,
                        ],
                        'unit_amount' => $item['producto']->precio * 100, // Convertir a centavos
                    ],
                    'quantity' => $item['cantidad'],
                ];
            }, $carrito),
            'mode' => 'payment',
            'success_url' => route('carrito.success'),
            'cancel_url' => route('carrito.cancel'),
        ]);

        return redirect($checkoutSession->url);
    }

    public function pagoExitoso()
    {
        // Aquí puedes registrar el pedido en la base de datos
        $carrito = Session::get('carrito');
        $total = array_reduce($carrito, function($carry, $item) {
            return $carry + $item['producto']->precio * $item['cantidad'];
        });

        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'total' => $total,
            'estado' => 'pagado'
        ]);

        // Registrar detalles del pedido (productos y cantidades)
        foreach ($carrito as $item) {
            $pedido->detalles()->create([
                'producto_id' => $item['producto']->id,
                'cantidad' => $item['cantidad'],
                'precio' => $item['producto']->precio,
            ]);
        }

        Session::forget('carrito');

        return view('carrito.success');
    }

    public function pagoCancelado()
    {
        return view('carrito.cancel');
    }
}
