@extends('layouts.app')

@section('content')
    <h1>Carrito de Compras</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(empty($cart))
        <p>El carrito está vacío.</p>
    @else
        <table>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
            @foreach($cart as $id => $item)
                <tr>
                    <td>{{ e($item['name']) }}</td> <!-- Previene XSS -->
                    <td>${{ number_format($item['price'], 2) }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                </tr>
            @endforeach
        </table>
        <form action="{{ route('cart.checkout') }}" method="POST">
            @csrf
            <button type="submit">Pagar</button>
        </form>
    @endif
@endsection
