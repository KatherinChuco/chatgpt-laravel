<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
</head>
<body>
    <h1>Tienda</h1>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <h2>Productos</h2>
    <ul>
        @foreach ($products as $product)
            <li>
                <strong>{{ $product->name }}</strong> - ${{ number_format($product->price, 2) }}
                <form action="{{ route('cart.add') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit">Agregar al carrito</button>
                </form>
            </li>
        @endforeach
    </ul>

    <h2>Carrito</h2>
    @if (!empty($cart))
        <ul>
            @foreach ($cart as $id => $item)
                <li>
                    <strong>{{ $item['name'] }}</strong> -
                    ${{ number_format($item['price'], 2) }} x {{ $item['quantity'] }} =
                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                    <form action="{{ route('cart.remove', $id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Eliminar</button>
                    </form>
                </li>
            @endforeach
        </ul>
        <p><strong>Total:</strong> ${{ number_format($total, 2) }}</p>
        <form action="{{ route('cart.clear') }}" method="POST">
            @csrf
            <button type="submit">Vaciar carrito</button>
        </form>
        <form action="{{ route('cart.checkout') }}" method="POST">
            @csrf
            <button type="submit">Procesar pago</button>
        </form>
    @else
        <p>El carrito está vacío.</p>
    @endif
</body>
</html>
