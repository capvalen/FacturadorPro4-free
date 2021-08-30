<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Config, DB;

class DocumentTest extends DuskTestCase
{

    use DatabaseMigrations;
    
    /**
    * @group documents
    */
    public function testDocument()
    {
        $this->browse(function (Browser $browser) {
            
 
             $this->artisan('db:seed', [
                '--class' => 'DatabaseSeeder'
            ]);
             
            
            $this->system_login($browser);
            $this->system_create_client($browser);
                
            // cliente
            Browser::$baseUrl = 'http://dev.'.env('APP_URL_BASE');
             
            $this->tenant_login($browser);           
                
            $this->product($browser);
            $this->customers($browser);
            $this->invoice($browser);
            $this->ticket($browser);
            
            $browser->pause(20);
                   
            $this->generate_annulation_invoice($browser);
            $this->generate_resumen($browser);


            $this->tenant_logout($browser);    
                
            // Change of url (Admin)
            Browser::$baseUrl = 'http://'.env('APP_URL_BASE');
            
            $this->system_delete_client($browser);

            $this->system_logout($browser);
            
            

        });
    }


    public function generate_annulation_invoice($browser){

        //create anulation
        $browser->waitForText('Anular', 20)
                ->press('Anular');


        $browser->waitForText('Comprobante: F001-1', 20)
                ->type('@description', 'error en el total')
                ->click('@annulment-voided')
                ->waitForText('La anulación RA-'.date('Ymd').'-1 fue creado correctamente', 25)
                ->assertSee('La anulación RA-'.date('Ymd').'-1 fue creado correctamente');


        $browser->visit('/voided');

        $browser->waitForText('Consultar', 20)
                ->click('@consult-voided')
                ->waitForText('La Comunicacion de baja RA-'.date('Ymd').'-1, ha sido aceptada', 25)
                ->assertSee('La Comunicacion de baja RA-'.date('Ymd').'-1, ha sido aceptada');

        $browser->pause(10);

    }
    public function generate_resumen($browser){
        
        $browser->visit('/summaries')
                ->waitForText('Nuevo', 20)
                ->press('Nuevo');

        $browser->waitForText('Registrar Resumen', 20)
                ->click('@search-documents');


        $browser->waitForText('Guardar', 20)
                ->click('@save-summary');
        
                

        $browser->waitForText('El resumen RC-'.date('Ymd').'-1 fue creado correctamente', 25)
                ->assertSee('El resumen RC-'.date('Ymd').'-1 fue creado correctamente');
    
        $browser->pause(15);
        
        $browser->waitForText('Acciones', 20)
                ->click('@consult-ticket');

        $browser->waitForText('El Resumen diario RC-'.date('Ymd').'-1, ha sido aceptado', 20)
                ->assertSee('El Resumen diario RC-'.date('Ymd').'-1, ha sido aceptado');

        $browser->visit('/documents')
                ->waitForText('Comprobantes', 20)
                ->assertSee('Comprobantes');

    }
    

    public function ticket($browser){
        //boleta          

        $browser->waitForText('Nuevo comprobante', 10)
                ->press('Nuevo comprobante') 
                ->waitForText('Tipo de comprobante', 5)
                ->click('@document_type_id')
                ->waitFor('@document_type_id')                
                ->elements('.el-select-document_type .el-select-dropdown__item')[1]->click();  

        $browser->click('@customer_id')
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

        
        $browser->waitForText('Comprobante: B001-1', 50)
                ->waitForText('Ir al listado', 20)
                ->assertSee('Comprobante: B001-1');
                
        $browser->elements('.el-button.list')[0]->click();



        
            
    }

    public function invoice($browser){

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
                    ->assertSee('Comprobante: F001-1');

    }

    public function customers($browser){
     

        $browser->clickLink('Clientes')
                ->press('Nuevo')
                ->waitForText('Nuevo Cliente', 5)
                ->click('@identity_document_type_id')
                ->waitFor('@identity_document_type_id')                
                ->elements('.el-select-identity_document_type .el-select-dropdown__item')[1]->click();                
            
        $browser->waitForText('RENIEC', 5)
                ->type('@number', '77695545')
                ->type('@name', 'Juan Perez');
        
        $browser->press('Guardar')
                ->waitForText('Cliente registrado con éxito', 10);


        $browser->press('Nuevo')
                ->waitForText('Nuevo Cliente', 5)
                ->type('@number', '20501973522')
                ->type('@name', 'Empresa SAC')
                ->type('@trade_name', 'Empresa SAC')
                ->click('@department_id')
                ->waitFor('@department_id')
                ->elements('.el-select-departments .el-select-dropdown__item')[0]->click();
                
        $browser->click('@province_id')
                ->waitFor('@province_id')
                ->elements('.el-select-provinces .el-select-dropdown__item')[0]->click();
            
        $browser->click('@district_id')
                ->waitFor('@district_id')
                ->elements('.el-select-districts .el-select-dropdown__item')[0]->click();
            
        $browser->type('@address', 'Alfa 125')
                ->type('@telephone', '4253366')
                ->type('@email', 'demo@gmail.com')
                ->press('Guardar')
                ->waitForText('Cliente registrado con éxito', 5);



    }

    public function product($browser){

        
        $browser->clickLink('Productos')
        ->press('Nuevo')
        ->waitForText('Nuevo Producto', 5)
        ->type('@internal_id', 'P001')
        ->type('@description', 'PEPSI')
        ->type('@item_code', '-')
        ->type('@sale_unit_price', '20')
        ->type('@purchase_unit_price', '20')
        ->press('Guardar')
        ->waitForText('Producto registrado con éxito', 5);

    }


    public function system_login($browser){

        $browser->visit('/')
                    ->assertSee('Acceso al Sistema')
                    ->type('email', 'admin@gmail.com')
                    ->type('password', '123456')
                    ->press('Iniciar sesión')
                    ->assertPathIs('/dashboard');
                
    }
    public function system_create_client($browser){

        
        $browser->press('Nuevo')
                ->assertSee('Nuevo Cliente')
                ->type('@number', '20501973522')
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
                // ->waitForText('20501973522', 300);
        
    }

    public function system_delete_client($browser){
 
        $browser->visit('/')
                ->waitForText('Eliminar', 5)
                ->press('Eliminar')
                ->waitForText('¿Desea eliminar el registro?', 5)
                ->elements('.el-message-box .el-button--primary')[0]->click();
    
        $browser->waitForText('Se eliminó correctamente el registro', 300);

    }

    public function system_logout($browser){
        
         
        $browser->clickLink('Admin Instrador')
                ->waitForText('Salir', 3)
                ->clickLink('Salir')
                ->assertSee('Acceso al Sistema');
                
    }
 
    public function tenant_login($browser){

        $browser->visit('/')
                ->type('email', 'test@test.com')
                ->type('password', '123456')
                ->press('Iniciar sesión')
                ->waitForText('Menu', 55);

    }
    public function tenant_logout($browser){
 
         $browser->clickLink('Administrador')
         ->waitForText('Salir', 3)
         ->clickLink('Salir')
         ->assertSee('Acceso al Sistema');

    } 
}
