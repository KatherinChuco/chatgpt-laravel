<!DOCTYPE html>
<html>
<head>
    <title>Carrito de Compras</title>
</head>
<body>
    <h1>Productos disponibles</h1>
    <ul>
        @foreach ($productos as $producto)
            <li>
                {{ $producto->nombre }} - ${{ $producto->precio }}
                <a href="{{ url('carrito/agregar/' . $producto->id) }}">Agregar al carrito</a>
            </li>
        @endforeach
    </ul>
    <hr>
    <a href="{{ route('carrito.ver') }}">Ver mi carrito</a>
</body>
</
