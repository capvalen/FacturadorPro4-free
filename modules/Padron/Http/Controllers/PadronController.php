<?php

namespace Modules\Padron\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Padron\Models\Padron;
use Modules\Padron\Models\ChargePadron;
use Modules\Padron\Traits\ExtractorTrait;
use Illuminate\Support\Facades\DB;


class PadronController extends Controller
{
    use ExtractorTrait;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
       // return view('padron::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
      //  return view('padron::create');
    }


    public function consulta($ruc)
    {
        if( strlen($ruc) != 11)
        {
            return [
                'success' => false,
                'message'=> "El número de RUC ingresado es inválido."
            ];
        }
        $site = Padron::where('ruc', $ruc)->first();
        if($site)
        {
            $response = (object)[
                'condicion' => $site->condicion_domicilio,
                'direccion' => "{$site->tipo_via} {$site->codigo_zona} {$site->tipo_zona} {$site->numero} {$site->interior}",
                'direccion_completa' =>  "{$site->tipo_via} {$site->codigo_zona} {$site->tipo_zona} {$site->numero} {$site->interior}",
                'estado' => $site->estado_contribuyente,
                'nombre_comercial' => $site->nombre_razon_social,
                'nombre_o_razon_social' => $site->nombre_razon_social,
                'ruc' => $site->ruc,
                'ubigeo' => [],
            ];

            return [
                'success' => true,
                'data' => $response
            ];
        }
        return [
            'success' => false,
            'message'=> "El número de RUC ingresado es inválido."
        ];
    }

    public function charges_data()
    {
        try{

            $history = ChargePadron::create([
                'reference' => 'FACTURADOR',
                'state' => 0
            ]);

            //DOWNLOAD ZIP ------
            $filepath = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.zip"));
            $url = "http://www2.sunat.gob.pe/padron_reducido_ruc.zip";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $raw_file_data = curl_exec($ch);
    
            if(curl_errno($ch)){
                 return 'error en descarga Zip';
            }
            
            curl_close($ch);
    
            file_put_contents($filepath, $raw_file_data);

            //EXTRACT ZIP
            $ini = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.zip"));
            $fin = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_extract".DIRECTORY_SEPARATOR.""));
            $data = $this->extract($ini, $fin);

            //LOAD DATA TXT TO MYSQL
            DB::connection('tenant')->table('padrones')->truncate();
           // $file = public_path();
            $file =  str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_extract".DIRECTORY_SEPARATOR."padron_reducido_ruc.txt"));
            $query = "LOAD DATA LOCAL INFILE '" . $file . "'
            INTO TABLE padrones FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES
                    (ruc,
                    nombre_razon_social,
                    estado_contribuyente,
                    condicion_domicilio,
                    ubigeo,
                    tipo_via,
                    codigo_zona,
                    tipo_zona,
                    numero,
                    interior,
                    lote,
                    departamento,
                    manzana,
                    kilometro,
                    @status,
                    @created_at,
                    @updated_at)
            SET status=1,created_at=NOW(),updated_at=null";
            DB::connection('tenant')->getpdo()->exec($query);


            $history->state = 1;
            $history->save();

            return [ 'success' => true, 'message' => 'Proceso Terminado correctamente'];

        }catch(Exception $e)
        {
            return [ 'success' => false, 'message' => $e->getMessage()];
        }
        catch(\PDOException $Exception ) {
            return [
                'success' => false,
                'message' => $Exception->getMessage(),
            ];
        }
       
    }

    public function list_history()
    {
        $data = ChargePadron::orderBy('created_at')->get();
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function demo()
    {

        try {
            DB::connection('tenant')->table('padrones')->truncate();
            //$file = public_path();
           
            $file =  str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_extract".DIRECTORY_SEPARATOR."demo.txt"));
            $query = "LOAD DATA LOCAL INFILE '" . $file . "'
            INTO TABLE padrones FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES
                    (ruc,
                    nombre_razon_social,
                    estado_contribuyente,
                    condicion_domicilio,
                    ubigeo,
                    tipo_via,
                    codigo_zona,
                    tipo_zona,
                    numero,
                    interior,
                    lote,
                    departamento,
                    manzana,
                    kilometro,
                    @status,
                    @created_at,
                    @updated_at)
            SET status=1,created_at=NOW(),updated_at=null";
            DB::connection('tenant')->getPdo()->exec($query);
    
            return 'ok';
        }
        catch(\PDOException $Exception ) {
            // Note The Typecast To An Integer!

            return [
                'message' => $Exception->getMessage(),
                'line' => $Exception->getLine(),
            ];
            //throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
        }
       
    }
}
