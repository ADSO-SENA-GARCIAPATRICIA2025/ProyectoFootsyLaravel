<?php

namespace Tests\Feature;

use App\Filament\Resources\Pedidos\Pages\CreatePedido;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Resources\Pedidos\PedidoResource;
use App\Models\Direccion;
use App\Models\Pedido;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol = 'cliente'): User
    {
        return User::factory()->create(['apellido' => 'Prueba', 'rolUsuario' => $rol, 'estadoActivo' => true]);
    }

    private function datos(?User $usuario = null): array
    {
        $usuario ??= $this->usuario();
        $direccion = Direccion::create([
            'id_usuario' => $usuario->id, 'nombreDestinatario' => 'Cliente Prueba',
            'direccion' => 'Calle original 123', 'ciudad' => 'Ciudad', 'pais' => 'Colombia',
        ]);

        return [
            'id_usuario' => $usuario->id, 'id_direccion' => $direccion->getKey(),
            'subtotal' => '100.10', 'descuento' => '10.05', 'impuestos' => '19.00', 'costoEnvio' => '4.95',
        ];
    }

    public function test_crud_admin_calcula_total_y_genera_orden_unica(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        $respuesta = $this->postJson('/api/pedidos', $datos)->assertCreated()
            ->assertJsonPath('total', '114.00')->assertJsonPath('estadoPedido', 'pendiente');
        $id = $respuesta->json('id_pedido');
        $numero = $respuesta->json('numeroOrden');
        $this->assertStringStartsWith('FT-', $numero);
        $otro = $this->postJson('/api/pedidos', $datos)->assertCreated();
        $this->assertNotSame($numero, $otro->json('numeroOrden'));
        $this->getJson('/api/pedidos')->assertOk()->assertJsonCount(2);
        $this->getJson('/api/pedidos/'.$id)->assertOk()->assertJsonPath('numeroOrden', $numero);
        $this->putJson('/api/pedidos/'.$id, ['descuento' => '0.00'])->assertOk()->assertJsonPath('total', '124.05');
        $this->deleteJson('/api/pedidos/'.$id)->assertOk();
        $this->getJson('/api/pedidos/'.$id)->assertNotFound();
    }

    public function test_exige_sesion_y_cliente_solo_consulta_sus_pedidos(): void
    {
        $this->getJson('/api/pedidos')->assertUnauthorized();
        $this->postJson('/api/pedidos', [])->assertUnauthorized();
        $cliente = $this->usuario();
        $propio = Pedido::create($this->datos($cliente));
        $ajeno = Pedido::create($this->datos());
        $this->actingAs($cliente);
        $this->getJson('/api/pedidos')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/pedidos/'.$propio->getKey())->assertOk();
        $this->getJson('/api/pedidos/'.$ajeno->getKey())->assertNotFound();
        $this->postJson('/api/pedidos', $this->datos($cliente))->assertForbidden();
        $this->putJson('/api/pedidos/'.$propio->getKey(), ['subtotal' => 1])->assertForbidden();
        $this->deleteJson('/api/pedidos/'.$propio->getKey())->assertForbidden();
        $cliente->update(['estadoActivo' => false]);
        $this->getJson('/api/pedidos')->assertForbidden();
    }

    public function test_valida_importes_direccion_y_campos_generados(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        foreach ([
            ['subtotal', -1], ['subtotal', '1.999'], ['descuento', '101.00'],
            ['impuestos', -1], ['costoEnvio', null], ['estadoPedido', 'inexistente'],
            ['numeroOrden', 'MANUAL'], ['total', '0.01'], ['direccionEntrega', ['direccion' => 'Falsa']],
            ['id_usuario', 999999], ['id_direccion', 999999],
        ] as [$campo, $valor]) {
            $this->postJson('/api/pedidos', array_merge($datos, [$campo => $valor]))
                ->assertUnprocessable()->assertJsonValidationErrors($campo);
        }
        $ajenos = $this->datos();
        $this->postJson('/api/pedidos', array_merge($datos, ['id_direccion' => $ajenos['id_direccion']]))
            ->assertUnprocessable()->assertJsonValidationErrors('id_direccion');
        Direccion::findOrFail($datos['id_direccion'])->update(['estadoActivo' => false]);
        $this->postJson('/api/pedidos', $datos)->assertUnprocessable()->assertJsonValidationErrors('id_direccion');
    }

    public function test_conserva_copia_de_entrega_y_no_permite_cambiar_dueno(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        $id = $this->postJson('/api/pedidos', $datos)->assertCreated()->json('id_pedido');
        Direccion::findOrFail($datos['id_direccion'])->update(['direccion' => 'Dirección nueva']);
        $this->getJson('/api/pedidos/'.$id)->assertOk()
            ->assertJsonPath('direccionEntrega.direccion', 'Calle original 123');
        $this->putJson('/api/pedidos/'.$id, ['id_usuario' => $this->usuario()->id])
            ->assertUnprocessable()->assertJsonValidationErrors('id_usuario');
        $this->putJson('/api/pedidos/'.$id, ['id_direccion' => $datos['id_direccion']])
            ->assertUnprocessable()->assertJsonValidationErrors('id_direccion');
    }

    public function test_transiciones_bloqueo_de_importes_y_eliminacion(): void
    {
        $this->actingAs($this->usuario('admin'));
        $id = $this->postJson('/api/pedidos', $this->datos())->assertCreated()->json('id_pedido');
        $url = '/api/pedidos/'.$id;
        $this->putJson($url, ['estadoPedido' => 'entregado'])->assertUnprocessable()->assertJsonValidationErrors('estadoPedido');
        $this->putJson($url, ['estadoPedido' => 'confirmado'])->assertOk();
        $this->putJson($url, ['estadoPedido' => 'pendiente'])->assertUnprocessable()->assertJsonValidationErrors('estadoPedido');
        $this->putJson($url, ['subtotal' => '1.00'])->assertUnprocessable()->assertJsonValidationErrors('subtotal');
        $this->deleteJson($url)->assertUnprocessable();
        $this->assertFalse(PedidoResource::canDelete(Pedido::findOrFail($id)));
        $this->putJson($url, ['estadoPedido' => 'en_preparacion'])->assertOk();
        $this->putJson($url, ['estadoPedido' => 'enviado'])->assertOk();
        $this->putJson($url, ['estadoPedido' => 'entregado'])->assertOk();
        $this->putJson($url, ['estadoPedido' => 'cancelado'])->assertUnprocessable();
    }

    public function test_calculo_en_centavos_y_limites_del_total(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = array_merge($this->datos(), ['subtotal' => '0.10', 'descuento' => 0, 'impuestos' => '0.20', 'costoEnvio' => 0]);
        $this->postJson('/api/pedidos', $datos)->assertCreated()->assertJsonPath('total', '0.30');
        $this->postJson('/api/pedidos', array_merge($datos, ['subtotal' => '9999999999.99']))
            ->assertUnprocessable()->assertJsonValidationErrors('subtotal');
    }

    public function test_filament_crea_edita_y_lista_pedidos(): void
    {
        $this->withoutVite();
        $this->actingAs($this->usuario('admin'));
        Filament::setCurrentPanel(Filament::getPanel('dashboard'));
        Livewire::test(CreatePedido::class)->fillForm($this->datos())
            ->call('create')->assertHasNoFormErrors();
        $pedido = Pedido::firstOrFail();
        Livewire::test(EditPedido::class, ['record' => $pedido->getKey()])
            ->fillForm(['descuento' => '0.00'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('124.05', $pedido->refresh()->total);
        Livewire::test(EditPedido::class, ['record' => $pedido->getKey()])
            ->fillForm(['estadoPedido' => 'confirmado'])->call('save')->assertHasNoFormErrors();
        Livewire::test(ListPedidos::class)->assertCanSeeTableRecords([$pedido->refresh()]);
    }
}
