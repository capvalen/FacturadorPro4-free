<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Config, DB;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample() {
        $this->browse(function (Browser $browser) {
            // Seeders
            $this->artisan('db:seed', [
                '--class' => 'DatabaseSeeder'
            ]);
            
            // Login (Admin)
            $browser->visit('/')
                ->assertSee('Acceso al Sistema')
                ->type('email', 'admin@gmail.com')
                ->type('password', '123456')
                ->press('Iniciar sesión')
                ->assertPathIs('/dashboard');
                
                

            // Customer registration (Admin)
            $browser->press('Nuevo')
                ->assertSee('Nuevo Cliente')
                ->type('@number', '12345678900')
                ->type('@name', 'TESTS S.A.S')
                ->type('@subdomain', 'dev')
                ->type('@email', 'test@test.com')
                ->type('@password', '123456')
                ->click('@plan_id')
                ->waitFor('@plan_id',10)
                ->elements('.el-select-dropdown__item')[0]->click();
            
            $browser->waitForText('Guardar', 5)
                ->press('Guardar')
                ->waitForText('Cliente Registrado satisfactoriamente', 300);
            
            // Change of url (Sub-domain)
            Browser::$baseUrl = 'http://dev.'.env('APP_URL_BASE');
            
            // Login (Sub-domain)
            $browser->visit('/')
                ->type('email', 'test@test.com')
                ->type('password', '123456')
                ->press('Iniciar sesión')
                ->waitForText('Menu', 35);

 

            // Create product (Sub-domain)
            $browser->clickLink('Productos')
                ->press('Nuevo')
                ->waitForText('Nuevo Producto', 5)
                ->type('@internal_id', '001')
                ->type('@description', 'Test product')
                ->type('@item_code', '-')
                ->type('@sale_unit_price', '15')
                ->type('@purchase_unit_price', '8')
                ->press('Guardar')
                ->waitForText('Producto registrado con éxito', 5);
            
            
            // Create client (Sub-domain)
            $browser->clickLink('Clientes')
                ->press('Nuevo')
                ->waitForText('Nuevo Cliente', 5)
                ->type('@number', '98765432100')
                ->type('@name', 'Test client')
                ->type('@trade_name', 'Test client')
                ->click('@department_id')
                ->waitFor('@department_id')
                ->elements('.el-select-departments .el-select-dropdown__item')[0]->click();
                
            $browser->click('@province_id')
                ->waitFor('@province_id')
                ->elements('.el-select-provinces .el-select-dropdown__item')[0]->click();
            
            $browser->click('@district_id')
                ->waitFor('@district_id')
                ->elements('.el-select-districts .el-select-dropdown__item')[0]->click();
            
            $browser->type('@address', 'Does not have')
                ->type('@telephone', '1234567')
                ->type('@email', 'nothing@nothing.com')
                ->press('Guardar')
                ->waitForText('Cliente registrado con éxito', 5);
            

            // Create electronic receipts (Sub-domain)             

            $browser->clickLink('Nuevo comprobante electrónico') 
                ->waitForText('Tipo de comprobante', 5)
                ->click('@customer_id')
                ->waitFor('@customer_id')
                ->elements('.el-select-customers .el-select-dropdown__item')[0]->click();
            
            $browser->press('+ Agregar Producto')
                ->waitForText('Agregar Producto o Servicio', 5)
                ->click('@item_id')
                ->waitFor('@item_id')
                ->elements('.el-select-items .el-select-dropdown__item')[0]->click();
            
            $browser->elements('.el-button.add')[0]->click();
            
            $browser->press('Cerrar')
                ->waitForText('Generar', 15) 
                ->press('Generar');

            
            $browser->waitForText('Comprobante: F001-1', 50)
                ->waitForText('Ir al listado', 20)
                ->elements('.el-button.list')[0]->click();
            

            //create anulation
            $browser->waitForText('Anular', 20)
                    ->press('Anular');


            $browser->waitForText('Comprobante: F001-1', 10)
                    ->type('@description', 'error en el total')
                    ->click('@annulment-voided')
                    ->waitForText('La anulación RA-20190214-1 fue creado correctamente', 25)
                    ->assertSee('La anulación RA-20190214-1 fue creado correctamente');


            $browser->visit('/voided');

            $browser->waitForText('Consultar', 20)
                    ->click('@consult-voided')
                    ->waitForText('La Comunicacion de baja RA-20190214-1, ha sido aceptada', 25)
                    ->assertSee('La Comunicacion de baja RA-20190214-1, ha sido aceptada');

            // $browser->clickLink('Anulaciones');


            // $browser->clickLink('Listado de comprobantes');



            // Logout (Sub-domain)
            $browser->clickLink('Administrador')
                ->waitForText('Salir', 3)
                ->clickLink('Salir')
                ->assertSee('Acceso al Sistema');
            
            // Change of url (Admin)
            Browser::$baseUrl = 'http://'.env('APP_URL_BASE');
            
            // Customer removal (Admin)
            $browser->visit('/')
                ->waitForText('Eliminar', 5)
                ->press('Eliminar')
                ->waitForText('¿Desea eliminar el registro?', 5)
                ->elements('.el-message-box .el-button--primary')[0]->click();
            
            $browser->waitForText('Se eliminó correctamente el registro', 300);
            
            // Logout (Admin)
            $browser->clickLink('Admin Instrador')
                ->waitForText('Salir', 3)
                ->clickLink('Salir')
                ->assertSee('Acceso al Sistema');
        });
    }
}
