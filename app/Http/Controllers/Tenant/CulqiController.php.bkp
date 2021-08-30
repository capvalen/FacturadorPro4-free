<?php
namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Culqi\Culqi;
use Culqi\CulqiException;
use Illuminate\Support\Facades\Mail;
use App\Mail\Tenant\CulqiEmail;
use stdClass;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant\Order;
use Illuminate\Support\Str;
use App\Models\Tenant\Person;
use Exception;




class CulqiController extends Controller
{

    public function __construct()
    {
        // $this->middleware('input.request:document,web', ['only' => ['store']]);
    }

    public function index()
    {
      
    }

    public function payment(Request $request)
    {
      try{

        $user = auth()->user();

        $SECRET_API_KEY = "sk_test_gZ9jAaILIsIweKfm";
        $culqi = new Culqi(array('api_key' => $SECRET_API_KEY));

        $charge = $culqi->Charges->create(
            array(
                "amount" => $request->precio,
                "currency_code" => "PEN",
                "email" => $request->email,
                "description" =>  $request->producto, 
                "source_id" => $request->token,
               //  "metadata" => array (
               //      "ruc" => $_POST['ruc'],
               //      "contacto" => $_POST['contacto'],
               //      "telefono" => $_POST['telefono']),
                "installments" => $request->installments
              )
        );

        $order = Order::create([
          'external_id' => Str::uuid()->toString(),
          'customer' => json_decode( $request->customer ),
          'shipping_address' => 'direccion 1',
          'items' => json_decode( $request->items ),
          'total' => $request->precio_culqi,
          'reference_payment' => 'culqui',
        ]);

         
        $customer_email = $request->email;
        $document = new stdClass;
        $document->client = $user->name;
        $document->product = $request->producto;
        $document->total = $request->precio_culqi;
        Mail::to($customer_email)->send(new CulqiEmail($document));

        return [
            'success' => true,
            'culqui' => $charge,
            'order' => $order,
        ];
      //  return json_encode($charge);
      }
      catch(Exception $e)
      {
        return [
            'success' => false,
            'message' =>  $e->getMessage()
        ];
      }

      

       
    }

    
      
}
