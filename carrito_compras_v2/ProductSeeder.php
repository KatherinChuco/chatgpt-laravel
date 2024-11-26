use App\Models\Product;

public function run()
{
    Product::create(['name' => 'Producto A', 'price' => 10.00, 'description' => 'Descripción del producto A']);
    Product::create(['name' => 'Producto B', 'price' => 15.00, 'description' => 'Descripción del producto B']);
    Product::create(['name' => 'Producto C', 'price' => 20.00, 'description' => 'Descripción del producto C']);
}
