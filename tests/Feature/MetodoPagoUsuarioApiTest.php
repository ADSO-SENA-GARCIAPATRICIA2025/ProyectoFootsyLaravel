<?php

namespace Tests\Feature;

use App\Models\MetodoPago;
use App\Models\MetodoPagoUsuario;
use App\Models\User;
use App\Filament\Resources\MetodoPagoUsuarios\Pages\CreateMetodoPagoUsuario;
use App\Filament\Resources\MetodoPagoUsuarios\Pages\EditMetodoPagoUsuario;
use App\Filament\Resources\MetodoPagoUsuarios\Pages\ListMetodoPagoUsuarios;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MetodoPagoUsuarioApiTest extends TestCase
{
    use RefreshDatabase;

    private function datos(): array
    {
        return [
            'id_metodo_pago' => MetodoPago::create(['nombre' => 'Tarjeta', 'proveedor' => 'Prueba'])->id_metodo_pago,
            'referenciaProveedor' => 'pm_prueba_1',
            'marca' => 'Visa',
            'ultimosCuatro' => '0042',
        ];
    }

    private function usuario(): User
    {
        return User::factory()->create(['apellido' => 'Prueba']);
    }

    public function test_exige_autenticacion_en_todas_las_rutas(): void
    {
        $this->getJson('/api/metodo-pago-usuarios')->assertUnauthorized();
        $this->getJson('/api/metodo-pago-usuarios/1')->assertUnauthorized();
        $this->postJson('/api/metodo-pago-usuarios', [])->assertUnauthorized();
        $this->putJson('/api/metodo-pago-usuarios/1', [])->assertUnauthorized();
        $this->deleteJson('/api/metodo-pago-usuarios/1')->assertUnauthorized();
    }

    public function test_crud_conserva_ceros_iniciales_y_oculta_la_referencia(): void
    {
        $usuario = $this->usuario();
        $this->actingAs($usuario);
        $datos = $this->datos();
        $respuesta = $this->postJson('/api/metodo-pago-usuarios', $datos)
            ->assertCreated()->assertJsonPath('ultimosCuatro', '0042')
            ->assertJsonPath('id_usuario', $usuario->id)->assertJsonPath('estadoActivo', true)
            ->assertJsonMissingPath('referenciaProveedor');
        $id = $respuesta->json('id_metodo_pago_usuario');
        $this->getJson('/api/metodo-pago-usuarios')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/metodo-pago-usuarios/'.$id)->assertOk()->assertJsonMissingPath('referenciaProveedor');
        $this->putJson('/api/metodo-pago-usuarios/'.$id, ['marca' => 'Otra'])
            ->assertOk()->assertJsonPath('marca', 'Otra');
        $this->assertSame(1, $usuario->metodosPagoUsuario()->count());
        $this->assertSame(1, MetodoPago::firstOrFail()->metodosPagoUsuario()->count());
        $this->deleteJson('/api/metodo-pago-usuarios/'.$id)->assertOk();
        $this->getJson('/api/metodo-pago-usuarios/'.$id)->assertNotFound();
    }

    public function test_no_permite_acceder_a_medios_de_otro_usuario_ni_cambiar_el_dueno(): void
    {
        $dueno = $this->usuario();
        $medio = MetodoPagoUsuario::create(array_merge($this->datos(), ['id_usuario' => $dueno->id]));
        $this->actingAs($this->usuario());
        $url = '/api/metodo-pago-usuarios/'.$medio->getKey();
        $this->getJson('/api/metodo-pago-usuarios')->assertOk()->assertJsonCount(0);
        $this->getJson($url)->assertNotFound();
        $this->putJson($url, ['marca' => 'Cambio'])->assertNotFound();
        $this->deleteJson($url)->assertNotFound();
        $this->postJson('/api/metodo-pago-usuarios', array_merge($this->datos(), ['id_usuario' => $dueno->id]))
            ->assertUnprocessable()->assertJsonValidationErrors('id_usuario');
        $this->assertModelExists($medio);
    }

    public function test_rechaza_duplicados_y_permite_editar_el_propio_registro(): void
    {
        $this->actingAs($this->usuario());
        $datos = $this->datos();
        $id = $this->postJson('/api/metodo-pago-usuarios', $datos)->assertCreated()->json('id_metodo_pago_usuario');
        $this->postJson('/api/metodo-pago-usuarios', $datos)
            ->assertUnprocessable()->assertJsonValidationErrors('referenciaProveedor');
        $this->putJson('/api/metodo-pago-usuarios/'.$id, $datos)->assertOk();
        $otroId = $this->postJson('/api/metodo-pago-usuarios', array_merge($datos, ['referenciaProveedor' => 'pm_otro']))
            ->assertCreated()->json('id_metodo_pago_usuario');
        $this->putJson('/api/metodo-pago-usuarios/'.$otroId, ['referenciaProveedor' => $datos['referenciaProveedor']])
            ->assertUnprocessable()->assertJsonValidationErrors('referenciaProveedor');
    }

    public function test_solo_un_principal_por_usuario_y_desactivar_lo_desmarca(): void
    {
        $usuario = $this->usuario();
        $otro = $this->usuario();
        $datos = $this->datos();
        $ajeno = MetodoPagoUsuario::create(array_merge($datos, ['id_usuario' => $otro->id, 'esPrincipal' => true]));
        $this->actingAs($usuario);
        $primero = $this->postJson('/api/metodo-pago-usuarios', array_merge($datos, ['esPrincipal' => true]))
            ->assertCreated()->json('id_metodo_pago_usuario');
        $segundo = $this->postJson('/api/metodo-pago-usuarios', array_merge($datos, [
            'referenciaProveedor' => 'pm_otro', 'esPrincipal' => true,
        ]))->assertCreated()->json('id_metodo_pago_usuario');
        $this->assertFalse(MetodoPagoUsuario::findOrFail($primero)->esPrincipal);
        $this->assertTrue($ajeno->refresh()->esPrincipal);
        $this->putJson('/api/metodo-pago-usuarios/'.$segundo, ['estadoActivo' => false])
            ->assertOk()->assertJsonPath('esPrincipal', false);
        $this->assertSame(0, $usuario->metodosPagoUsuario()->where('esPrincipal', true)->count());
    }

    public function test_valida_campos_longitudes_y_metodo_existente(): void
    {
        $this->actingAs($this->usuario());
        $this->postJson('/api/metodo-pago-usuarios', [])
            ->assertUnprocessable()->assertJsonValidationErrors(['id_metodo_pago', 'referenciaProveedor']);
        $datos = $this->datos();
        foreach ([
            ['id_metodo_pago', 999999], ['referenciaProveedor', str_repeat('x', 101)],
            ['marca', str_repeat('x', 51)], ['ultimosCuatro', '12a4'],
            ['ultimosCuatro', '123'], ['ultimosCuatro', '12345'], ['esPrincipal', 'si'],
            ['estadoActivo', null],
        ] as [$campo, $valor]) {
            $this->postJson('/api/metodo-pago-usuarios', array_merge($datos, [$campo => $valor]))
                ->assertUnprocessable()->assertJsonValidationErrors($campo);
        }
        $this->postJson('/api/metodo-pago-usuarios', array_merge($datos, ['marca' => null, 'ultimosCuatro' => null]))
            ->assertCreated();
    }

    public function test_filament_crea_edita_lista_y_valida_duplicados(): void
    {
        $this->withoutVite();
        $admin = User::factory()->create(['apellido' => 'Admin', 'rolUsuario' => 'admin']);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('dashboard'));
        $datos = array_merge($this->datos(), ['id_usuario' => $admin->id, 'esPrincipal' => true]);

        Livewire::test(CreateMetodoPagoUsuario::class)
            ->fillForm($datos)->call('create')->assertHasNoFormErrors();
        $medio = MetodoPagoUsuario::firstOrFail();
        Livewire::test(CreateMetodoPagoUsuario::class)
            ->fillForm($datos)->call('create')->assertHasFormErrors(['referenciaProveedor' => 'unique']);
        Livewire::test(EditMetodoPagoUsuario::class, ['record' => $medio->getKey()])
            ->fillForm(['marca' => 'Mastercard'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('Mastercard', $medio->refresh()->marca);
        Livewire::test(ListMetodoPagoUsuarios::class)->assertCanSeeTableRecords([$medio]);
    }
}
