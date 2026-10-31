<?php

namespace Tests\Feature;

use App\Models\Carrito;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use App\Models\VarianteProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCarritoApiTest extends TestCase
{
    use RefreshDatabase;

    private function datosItem(): array
    {
        $usuario = User::factory()->create(['apellido' => 'Prueba']);
        $carrito = Carrito::create(['id_usuario' => $usuario->id]);
        $categoria = Categoria::create(['nombre' => 'Calzado']);
        $producto = Producto::create([
            'codigoProducto' => 'TEST-1',
            'nombreProducto' => 'Zapato de prueba',
            'marca' => 'Footsy',
            'precioVenta' => 50,
            'publicoObjetivo' => 'unisex',
            'id_categoria' => $categoria->id_categoria,
        ]);
        $variante = VarianteProducto::create([
            'color' => 'Negro', 'talla' => '38', 'stock' => 3,
            'id_producto' => $producto->id_producto,
        ]);

        return ['id_carrito' => $carrito->id_carrito, 'id_variante' => $variante->id_variante, 'cantidad' => 2];
    }

    public function test_crud_y_edicion_sin_cambiar_la_variante(): void
    {
        $datos = $this->datosItem();
        $respuesta = $this->postJson('/api/item-carritos', $datos)->assertCreated();
        $id = $respuesta->json('id_item_carrito');

        $this->getJson('/api/item-carritos')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/item-carritos/'.$id)
            ->assertOk()->assertJsonPath('variante.producto.nombreProducto', 'Zapato de prueba');
        $this->putJson('/api/item-carritos/'.$id, ['cantidad' => 3])
            ->assertOk()->assertJsonPath('cantidad', 3);
        $this->deleteJson('/api/item-carritos/'.$id)->assertOk();
        $this->getJson('/api/item-carritos/'.$id)->assertNotFound();
    }

    public function test_rechaza_duplicados_pero_permite_la_variante_en_otro_carrito(): void
    {
        $datos = $this->datosItem();
        $this->postJson('/api/item-carritos', $datos)->assertCreated();
        $this->postJson('/api/item-carritos', $datos)
            ->assertUnprocessable()->assertJsonValidationErrors('id_variante');

        $otro = Carrito::create(['id_usuario' => User::factory()->create(['apellido' => 'Otro'])->id]);
        $datos['id_carrito'] = $otro->id_carrito;
        $id = $this->postJson('/api/item-carritos', $datos)->assertCreated()->json('id_item_carrito');
        $primerCarrito = Carrito::where('id_carrito', '!=', $otro->id_carrito)->firstOrFail();
        $this->putJson('/api/item-carritos/'.$id, ['id_carrito' => $primerCarrito->id_carrito])
            ->assertUnprocessable()->assertJsonValidationErrors('id_variante');
    }

    public function test_valida_cantidad_stock_y_claves_foraneas(): void
    {
        $datos = $this->datosItem();
        foreach ([0, -1, 1.5, 4] as $cantidad) {
            $this->postJson('/api/item-carritos', array_merge($datos, ['cantidad' => $cantidad]))
                ->assertUnprocessable()->assertJsonValidationErrors('cantidad');
        }
        $this->postJson('/api/item-carritos', array_merge($datos, ['id_carrito' => 999999]))
            ->assertUnprocessable()->assertJsonValidationErrors('id_carrito');
        $this->postJson('/api/item-carritos', array_merge($datos, ['id_variante' => 999999]))
            ->assertUnprocessable()->assertJsonValidationErrors('id_variante');
        $id = $this->postJson('/api/item-carritos', $datos)->assertCreated()->json('id_item_carrito');
        $this->putJson('/api/item-carritos/'.$id, ['cantidad' => 4])
            ->assertUnprocessable()->assertJsonValidationErrors('cantidad');

        VarianteProducto::findOrFail($datos['id_variante'])->update(['stock' => 0]);
        $this->putJson('/api/item-carritos/'.$id, ['cantidad' => 1])
            ->assertUnprocessable()->assertJsonValidationErrors('cantidad');
    }
}
