<?php

namespace Tests\Feature;

 use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


public function test_lista_de_productos_responde_correctamente(): void
    {
        $response = $this->getJson('/api/productos');

        $response->assertStatus(200);
    }

    public function test_ruta_inexistente_devuelve_error_404(): void
    {
        $response = $this->get('/pagina-que-no-existe');

        $response->assertStatus(404);
    }

}
