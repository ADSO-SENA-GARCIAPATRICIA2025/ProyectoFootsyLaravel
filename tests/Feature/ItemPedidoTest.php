<?php

namespace Tests\Feature;

use App\Filament\Resources\ItemPedidos\ItemPedidoResource;
use App\Filament\Resources\ItemPedidos\Pages\CreateItemPedido;
use App\Filament\Resources\ItemPedidos\Pages\EditItemPedido;
use App\Filament\Resources\ItemPedidos\Pages\ListItemPedidos;
use App\Models\Categoria;
use App\Models\Direccion;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use App\Models\VarianteProducto;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ItemPedidoTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol = 'cliente'): User
    {
        return User::factory()->create(['apellido' => 'Prueba', 'rolUsuario' => $rol, 'estadoActivo' => true]);
    }

    private function pedido(?User $usuario = null): Pedido
    {
        $usuario ??= $this->usuario();
        $direccion = Direccion::create([
            'id_usuario' => $usuario->id, 'nombreDestinatario' => 'Prueba',
            'direccion' => 'Calle 1', 'ciudad' => 'Ciudad', 'pais' => 'Colombia',
        ]);

        return Pedido::create(['id_usuario' => $usuario->id, 'id_direccion' => $direccion->getKey(), 'subtotal' => '0.00']);
    }

    private function variante(): VarianteProducto
    {
        $categoria = Categoria::create(['nombre' => 'Calzado']);
        $producto = Producto::create([
            'codigoProducto' => 'TEST-' . $categoria->getKey(), 'nombreProducto' => 'Zapato',
            'marca' => 'Footsy', 'precioVenta' => '10.15', 'estadoActivo' => true,
            'publicoObjetivo' => 'unisex', 'id_categoria' => $categoria->getKey(),
        ]);

        return VarianteProducto::create([
            'id_producto' => $producto->getKey(), 'talla' => '38', 'color' => 'Negro',
            'stock' => 10, 'estadoActivo' => true,
        ]);
    }

    private function datos(Pedido $pedido, VarianteProducto $variante): array
    {
        return ['id_pedido' => $pedido->getKey(), 'id_variante' => $variante->getKey(), 'cantidad' => 2];
    }

    public function test_crud_recalcula_pedido_y_conserva_precio_al_cambiar_el_catalogo(): void
    {
        $this->actingAs($this->usuario('admin'));
        $pedido = $this->pedido();
        $variante = $this->variante();
        $respuesta = $this->postJson('/api/item-pedidos', $this->datos($pedido, $variante))
            ->assertCreated()->assertJsonPath('precioUnitario', '10.15')->assertJsonPath('subtotalLinea', '20.30');
        $id = $respuesta->json('id_item_pedido');
        $this->assertSame('20.30', $pedido->refresh()->subtotal);
        $pedido->update(['descuento' => '1.00', 'impuestos' => '2.00', 'costoEnvio' => '3.00']);
        $this->assertSame('24.30', $pedido->refresh()->total);
        $variante->producto->update(['precioVenta' => '99.99']);
        $this->putJson('/api/item-pedidos/'.$id, ['cantidad' => 3])->assertOk()
            ->assertJsonPath('precioUnitario', '10.15')->assertJsonPath('subtotalLinea', '30.45');
        $this->assertSame('34.45', $pedido->refresh()->total);
        $this->getJson('/api/item-pedidos')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/item-pedidos/'.$id)->assertOk();
        $pedido->update(['descuento' => 0]);
        $this->deleteJson('/api/item-pedidos/'.$id)->assertOk();
        $this->assertSame('0.00', $pedido->refresh()->subtotal);
        $this->assertSame('5.00', $pedido->total);
        $this->getJson('/api/item-pedidos/'.$id)->assertNotFound();
    }

    public function test_permisos_y_aislamiento_entre_clientes(): void
    {
        $this->getJson('/api/item-pedidos')->assertUnauthorized();
        $this->postJson('/api/item-pedidos', [])->assertUnauthorized();
        $cliente = $this->usuario();
        $propio = ItemPedido::create($this->datos($this->pedido($cliente), $this->variante()));
        $ajeno = ItemPedido::create($this->datos($this->pedido(), $this->variante()));
        $this->actingAs($cliente);
        $this->getJson('/api/item-pedidos')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/item-pedidos/'.$propio->getKey())->assertOk();
        $this->getJson('/api/item-pedidos/'.$ajeno->getKey())->assertNotFound();
        $this->postJson('/api/item-pedidos', [])->assertForbidden();
        $this->putJson('/api/item-pedidos/'.$propio->getKey(), ['cantidad' => 1])->assertForbidden();
        $this->deleteJson('/api/item-pedidos/'.$propio->getKey())->assertForbidden();
    }

    public function test_valida_cantidad_claves_y_no_acepta_importes_del_cliente(): void
    {
        $this->actingAs($this->usuario('admin'));
        $pedido = $this->pedido();
        $variante = $this->variante();
        $datos = $this->datos($pedido, $variante);
        foreach ([['cantidad', 0], ['cantidad', -1], ['cantidad', 1.5], ['cantidad', 11],
            ['id_pedido', 999999], ['id_variante', 999999], ['precioUnitario', 1], ['subtotalLinea', 1]] as [$campo, $valor]) {
            $this->postJson('/api/item-pedidos', array_merge($datos, [$campo => $valor]))
                ->assertUnprocessable()->assertJsonValidationErrors($campo);
        }
        $id = $this->postJson('/api/item-pedidos', $datos)->assertCreated()->json('id_item_pedido');
        $this->putJson('/api/item-pedidos/'.$id, ['id_pedido' => $pedido->getKey()])
            ->assertUnprocessable()->assertJsonValidationErrors('id_pedido');
        $this->putJson('/api/item-pedidos/'.$id, ['precioUnitario' => 1])
            ->assertUnprocessable()->assertJsonValidationErrors('precioUnitario');
    }

    public function test_evitar_duplicados_en_mismo_pedido_pero_permitir_otros(): void
    {
        $this->actingAs($this->usuario('admin'));
        $variante = $this->variante();
        $datos = $this->datos($this->pedido(), $variante);
        $this->postJson('/api/item-pedidos', $datos)->assertCreated();
        $this->postJson('/api/item-pedidos', $datos)->assertUnprocessable()->assertJsonValidationErrors('id_variante');
        $this->postJson('/api/item-pedidos', $this->datos($this->pedido(), $variante))->assertCreated();
    }

    public function test_pedido_confirmado_bloquea_modificar_el_detalle(): void
    {
        $this->actingAs($this->usuario('admin'));
        $pedido = $this->pedido();
        $variante = $this->variante();
        $item = ItemPedido::create($this->datos($pedido, $variante));
        $pedido->refresh()->update(['estadoPedido' => 'confirmado']);
        $this->putJson('/api/item-pedidos/'.$item->getKey(), ['cantidad' => 1])
            ->assertUnprocessable()->assertJsonValidationErrors('id_pedido');
        $this->deleteJson('/api/item-pedidos/'.$item->getKey())->assertUnprocessable();
        $this->postJson('/api/item-pedidos', $this->datos($pedido, $this->variante()))->assertUnprocessable();
        $this->assertFalse(ItemPedidoResource::canEdit($item));
        $this->assertFalse(ItemPedidoResource::canDelete($item));
    }

    public function test_revierte_cambio_y_eliminacion_si_el_descuento_supera_nuevo_subtotal(): void
    {
        $this->actingAs($this->usuario('admin'));
        $pedido = $this->pedido();
        $item = ItemPedido::create($this->datos($pedido, $this->variante()));
        $pedido->refresh()->update(['descuento' => '15.00']);
        $this->putJson('/api/item-pedidos/'.$item->getKey(), ['cantidad' => 1])
            ->assertUnprocessable()->assertJsonValidationErrors('descuento');
        $this->assertSame(2, $item->refresh()->cantidad);
        $this->assertSame('20.30', $pedido->refresh()->subtotal);
        $this->deleteJson('/api/item-pedidos/'.$item->getKey())->assertUnprocessable();
        $this->assertModelExists($item);
        $this->assertSame('5.30', $pedido->refresh()->total);
    }

    public function test_suma_varias_lineas_y_no_permite_sobrescribir_subtotal_de_cabecera(): void
    {
        $this->actingAs($this->usuario('admin'));
        $pedido = $this->pedido();
        $primero = ItemPedido::create($this->datos($pedido, $this->variante()));
        $segundo = ItemPedido::create($this->datos($pedido, $this->variante()));
        $this->assertSame('40.60', $pedido->refresh()->subtotal);
        $this->putJson('/api/pedidos/'.$pedido->getKey(), ['subtotal' => '1.00'])
            ->assertOk()->assertJsonPath('subtotal', '40.60');
        $primero->delete();
        $this->assertSame('20.30', $pedido->refresh()->subtotal);
        $this->assertSame(1, $segundo->variante->itemsPedido()->count());
    }

    public function test_producto_inactivo_y_limite_del_subtotal(): void
    {
        $this->actingAs($this->usuario('admin'));
        $pedido = $this->pedido();
        $variante = $this->variante();
        $variante->producto->update(['estadoActivo' => false]);
        $this->postJson('/api/item-pedidos', $this->datos($pedido, $variante))
            ->assertUnprocessable()->assertJsonValidationErrors('id_variante');
        $variante->producto->update(['estadoActivo' => true, 'precioVenta' => '99999999.99']);
        $variante->update(['stock' => 10001]);
        $this->postJson('/api/item-pedidos', array_merge($this->datos($pedido, $variante), ['cantidad' => 10001]))
            ->assertUnprocessable()->assertJsonValidationErrors('cantidad');
        $this->assertSame(0, ItemPedido::count());
    }

    public function test_filament_crea_valida_edita_y_lista(): void
    {
        $this->withoutVite();
        $this->actingAs($this->usuario('admin'));
        Filament::setCurrentPanel(Filament::getPanel('dashboard'));
        $pedido = $this->pedido();
        $datos = $this->datos($pedido, $this->variante());
        Livewire::test(CreateItemPedido::class)->fillForm($datos)->call('create')->assertHasNoFormErrors();
        Livewire::test(CreateItemPedido::class)->fillForm($datos)->call('create')->assertHasFormErrors(['id_variante' => 'unique']);
        $item = ItemPedido::firstOrFail();
        Livewire::test(EditItemPedido::class, ['record' => $item->getKey()])
            ->fillForm(['cantidad' => 3])->call('save')->assertHasNoFormErrors();
        $this->assertSame('30.45', $pedido->refresh()->subtotal);
        $pedido->update(['descuento' => '25.00']);
        Livewire::test(EditItemPedido::class, ['record' => $item->getKey()])
            ->fillForm(['cantidad' => 1])->call('save')->assertHasFormErrors(['cantidad']);
        Livewire::test(ListItemPedidos::class)->assertCanSeeTableRecords([$item->refresh()]);
    }
}
