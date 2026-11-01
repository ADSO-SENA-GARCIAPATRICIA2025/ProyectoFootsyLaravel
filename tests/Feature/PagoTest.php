<?php

namespace Tests\Feature;

use App\Filament\Resources\Pagos\Pages\CreatePago;
use App\Filament\Resources\Pagos\Pages\EditPago;
use App\Filament\Resources\Pagos\Pages\ListPagos;
use App\Filament\Resources\Pagos\PagoResource;
use App\Models\Direccion;
use App\Models\Categoria;
use App\Models\ItemPedido;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use App\Models\VarianteProducto;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PagoTest extends TestCase
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

        return Pedido::create(['id_usuario' => $usuario->id, 'id_direccion' => $direccion->getKey(), 'subtotal' => '100.00']);
    }

    private function datos(?Pedido $pedido = null): array
    {
        return [
            'id_pedido' => ($pedido ?? $this->pedido())->getKey(),
            'id_metodo_pago' => MetodoPago::create(['nombre' => 'Transferencia', 'estadoActivo' => true])->getKey(),
            'monto' => '100.00',
        ];
    }

    public function test_crea_consulta_aprueba_y_conserva_la_fecha(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        $respuesta = $this->postJson('/api/pagos', $datos)->assertCreated()
            ->assertJsonPath('estadoPago', 'pendiente')->assertJsonPath('fechaPago', null);
        $id = $respuesta->json('id_pago');
        $this->getJson('/api/pagos')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/pagos/'.$id)->assertOk()->assertJsonPath('pedido.id_pedido', $datos['id_pedido']);
        $aprobado = $this->putJson('/api/pagos/'.$id, [
            'estadoPago' => 'aprobado', 'referenciaTransaccion' => 'TX-PRUEBA-1',
        ])->assertOk()->assertJsonPath('estadoPago', 'aprobado');
        $fecha = $aprobado->json('fechaPago');
        $this->assertNotNull($fecha);
        $this->travel(1)->minutes();
        $this->putJson('/api/pagos/'.$id, [])->assertOk()->assertJsonPath('fechaPago', $fecha);
        $this->putJson('/api/pagos/'.$id, ['estadoPago' => 'pendiente'])
            ->assertUnprocessable()->assertJsonValidationErrors('estadoPago');
        $this->putJson('/api/pagos/'.$id, ['referenciaTransaccion' => 'CAMBIO'])
            ->assertUnprocessable()->assertJsonValidationErrors('estadoPago');
    }

    public function test_requiere_sesion_y_cliente_solo_ve_sus_pagos(): void
    {
        $this->getJson('/api/pagos')->assertUnauthorized();
        $this->postJson('/api/pagos', [])->assertUnauthorized();
        $cliente = $this->usuario();
        $propio = Pago::create($this->datos($this->pedido($cliente)));
        $ajeno = Pago::create($this->datos());
        $this->actingAs($cliente);
        $this->getJson('/api/pagos')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/pagos/'.$propio->getKey())->assertOk();
        $this->getJson('/api/pagos/'.$ajeno->getKey())->assertNotFound();
        $this->postJson('/api/pagos', [])->assertForbidden();
        $this->putJson('/api/pagos/'.$propio->getKey(), ['estadoPago' => 'aprobado'])->assertForbidden();
        $this->deleteJson('/api/pagos/'.$propio->getKey())->assertForbidden();
        $cliente->update(['estadoActivo' => false]);
        $this->getJson('/api/pagos')->assertForbidden();
    }

    public function test_valida_importes_estado_claves_y_fechas(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        foreach ([['monto', 0], ['monto', -1], ['monto', '1.999'], ['monto', '101.00'],
            ['monto', '10000000000.00'], ['id_pedido', 999999], ['id_metodo_pago', 999999],
            ['estadoPago', 'desconocido'], ['estadoPago', 'aprobado'],
            ['referenciaTransaccion', str_repeat('x', 256)], ['fechaPago', '2026-01-01'],
            ['fechaCreacion', '2026-01-01']] as [$campo, $valor]) {
            $this->postJson('/api/pagos', array_merge($datos, [$campo => $valor]))
                ->assertUnprocessable()->assertJsonValidationErrors($campo);
        }
        MetodoPago::findOrFail($datos['id_metodo_pago'])->update(['estadoActivo' => false]);
        $this->postJson('/api/pagos', $datos)->assertUnprocessable()->assertJsonValidationErrors('id_metodo_pago');
    }

    public function test_pagos_parciales_no_superan_el_saldo_al_aprobar(): void
    {
        $this->actingAs($this->usuario('admin'));
        $pedido = $this->pedido();
        $datos = array_merge($this->datos($pedido), ['monto' => '60.00']);
        $primero = $this->postJson('/api/pagos', $datos)->assertCreated()->json('id_pago');
        $segundo = $this->postJson('/api/pagos', $datos)->assertCreated()->json('id_pago');
        $this->putJson('/api/pagos/'.$primero, ['estadoPago' => 'aprobado'])->assertOk();
        $this->putJson('/api/pagos/'.$segundo, ['estadoPago' => 'aprobado'])
            ->assertUnprocessable()->assertJsonValidationErrors('monto');
        $this->assertSame('pendiente', Pago::findOrFail($segundo)->estadoPago);
        $this->putJson('/api/pagos/'.$segundo, ['estadoPago' => 'rechazado'])->assertOk()->assertJsonPath('fechaPago', null);
        $resto = $this->postJson('/api/pagos', array_merge($datos, ['monto' => '40.00']))->assertCreated()->json('id_pago');
        $this->putJson('/api/pagos/'.$resto, ['estadoPago' => 'aprobado'])->assertOk();
        $this->postJson('/api/pagos', array_merge($datos, ['monto' => '0.01']))
            ->assertUnprocessable()->assertJsonValidationErrors('monto');
    }

    public function test_referencia_unica_por_metodo_y_admite_varios_intentos_sin_referencia(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        $uno = $this->postJson('/api/pagos', $datos)->assertCreated()->json('id_pago');
        $this->postJson('/api/pagos', $datos)->assertCreated();
        $this->putJson('/api/pagos/'.$uno, ['referenciaTransaccion' => 'TX-UNICA'])->assertOk();
        $otroPedido = $this->pedido();
        $this->postJson('/api/pagos', array_merge($datos, [
            'id_pedido' => $otroPedido->getKey(), 'referenciaTransaccion' => 'TX-UNICA',
        ]))->assertUnprocessable()->assertJsonValidationErrors('referenciaTransaccion');
        $otroMetodo = $this->datos($otroPedido);
        $this->postJson('/api/pagos', array_merge($otroMetodo, ['referenciaTransaccion' => 'TX-UNICA']))->assertCreated();
    }

    public function test_historial_no_se_borra_y_pedido_con_pagos_no_se_elimina(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        $id = $this->postJson('/api/pagos', $datos)->assertCreated()->json('id_pago');
        $this->deleteJson('/api/pagos/'.$id)->assertUnprocessable();
        $this->deleteJson('/api/pedidos/'.$datos['id_pedido'])->assertUnprocessable();
        $this->putJson('/api/pagos/'.$id, ['estadoPago' => 'cancelado'])->assertOk()->assertJsonPath('fechaPago', null);
        $this->deleteJson('/api/pagos/'.$id)->assertUnprocessable();
        $this->assertModelExists(Pago::findOrFail($id));
        $this->assertFalse(PagoResource::canDeleteAny());
        $this->assertFalse(PagoResource::canEdit(Pago::findOrFail($id)));
    }

    public function test_bloquea_importes_del_pedido_y_cambios_en_datos_fijos_del_pago(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        $id = $this->postJson('/api/pagos', $datos)->assertCreated()->json('id_pago');
        $urlPedido = '/api/pedidos/'.$datos['id_pedido'];
        $this->putJson($urlPedido, ['subtotal' => '90.00'])->assertUnprocessable()->assertJsonValidationErrors('subtotal');
        foreach (['id_pedido' => $datos['id_pedido'], 'id_metodo_pago' => $datos['id_metodo_pago'], 'monto' => '90.00', 'fechaPago' => '2026-01-01'] as $campo => $valor) {
            $this->putJson('/api/pagos/'.$id, [$campo => $valor])
                ->assertUnprocessable()->assertJsonValidationErrors($campo);
        }
        $this->putJson('/api/pagos/'.$id, ['estadoPago' => 'cancelado'])->assertOk();
        $this->putJson($urlPedido, ['subtotal' => '90.00'])->assertOk();
        $nuevo = $this->postJson('/api/pagos', array_merge($datos, ['monto' => '90.00']))->assertCreated()->json('id_pago');
        $this->putJson('/api/pagos/'.$nuevo, ['estadoPago' => 'aprobado'])->assertOk();
        $this->putJson($urlPedido, ['estadoPedido' => 'cancelado'])->assertUnprocessable()->assertJsonValidationErrors('estadoPedido');
    }

    public function test_pedido_cancelado_no_admite_pagos_ni_aprobar_intentos_anteriores(): void
    {
        $this->actingAs($this->usuario('admin'));
        $datos = $this->datos();
        $id = $this->postJson('/api/pagos', $datos)->assertCreated()->json('id_pago');
        $this->putJson('/api/pedidos/'.$datos['id_pedido'], ['estadoPedido' => 'cancelado'])->assertOk();
        $this->postJson('/api/pagos', $datos)->assertUnprocessable()->assertJsonValidationErrors('id_pedido');
        $this->putJson('/api/pagos/'.$id, ['estadoPago' => 'aprobado'])->assertUnprocessable();
        $this->putJson('/api/pagos/'.$id, ['estadoPago' => 'cancelado'])->assertOk();
    }

    public function test_cambio_en_detalle_se_revierte_si_hay_un_pago_pendiente(): void
    {
        $this->actingAs($this->usuario('admin'));
        $pedido = $this->pedido();
        $categoria = Categoria::create(['nombre' => 'Calzado']);
        $producto = Producto::create([
            'codigoProducto' => 'PAGO-PRUEBA', 'nombreProducto' => 'Zapato', 'marca' => 'Footsy',
            'precioVenta' => '50.00', 'estadoActivo' => true, 'publicoObjetivo' => 'unisex',
            'id_categoria' => $categoria->getKey(),
        ]);
        $variante = VarianteProducto::create([
            'id_producto' => $producto->getKey(), 'color' => 'Negro', 'talla' => '38', 'stock' => 10, 'estadoActivo' => true,
        ]);
        $item = ItemPedido::create(['id_pedido' => $pedido->getKey(), 'id_variante' => $variante->getKey(), 'cantidad' => 2]);
        Pago::create($this->datos($pedido));
        $this->putJson('/api/item-pedidos/'.$item->getKey(), ['cantidad' => 1])->assertUnprocessable();
        $this->deleteJson('/api/item-pedidos/'.$item->getKey())->assertUnprocessable();
        $this->assertSame(2, $item->refresh()->cantidad);
        $this->assertSame('100.00', $pedido->refresh()->total);
    }

    public function test_filament_crea_valida_aprueba_y_lista(): void
    {
        $this->withoutVite();
        $this->actingAs($this->usuario('admin'));
        Filament::setCurrentPanel(Filament::getPanel('dashboard'));
        $datos = $this->datos();
        Livewire::test(CreatePago::class)->fillForm(array_merge($datos, ['monto' => '101.00']))
            ->call('create')->assertHasFormErrors(['monto']);
        Livewire::test(CreatePago::class)->fillForm($datos)->call('create')->assertHasNoFormErrors();
        $pago = Pago::firstOrFail();
        Livewire::test(EditPago::class, ['record' => $pago->getKey()])
            ->fillForm(['estadoPago' => 'aprobado', 'referenciaTransaccion' => 'TX-FILAMENT'])
            ->call('save')->assertHasNoFormErrors();
        $this->assertNotNull($pago->refresh()->fechaPago);
        Livewire::test(ListPagos::class)->assertCanSeeTableRecords([$pago]);
    }
}
