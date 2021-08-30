<?php
namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Item;
use App\Http\Resources\Tenant\ItemCollection;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant\User;
use App\Models\Tenant\Person;
use Illuminate\Support\Str;
use App\Models\Tenant\Order;
use App\Models\Tenant\ItemsRating;

use stdClass;
use Illuminate\Support\Facades\Mail;
use App\Mail\Tenant\CulqiEmail;



class EcommerceController extends Controller
{

    public function index()
    {
        return view('tenant.ecommerce.index');
    }

    public function item($id)
    {
        $row = Item::find($id);
        $record = (object)[
            'id' => $row->id,
            'description' => $row->description,
            'name' => $row->name,
            'second_name' => $row->second_name,
            'sale_unit_price' => $row->sale_unit_price,
            'image' =>  $row->image,
            'image_medium' => $row->image_medium,
            'image_small' => $row->image_small
        ];
        return view('tenant.ecommerce.items.record', compact('record'));
    }

    public function items()
    {
        $records = Item::where('apply_store', 1)->get();
        return view('tenant.ecommerce.items.index', compact('records'));
    }

    public function itemsBar()
    {
        $records = Item::where('apply_store', 1)->get();
        return new ItemCollection($records);
       
    }

    public function partialItem($id)
    {   
        $record = Item::find($id);
        return view('tenant.ecommerce.items.partial', compact('record'));
    }

    public function detailCart()
    {
        return view('tenant.ecommerce.cart.detail');
    }

    public function pay()
    {
        return view('tenant.ecommerce.cart.pay');
    }

    public function showLogin()
    {
        return view('tenant.ecommerce.user.login');
    }

    public function login(Request $request)
    {   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
           return[
               'success' => true,
               'message' => 'Login Success'
           ];
        }
        else{
            return[
                'success' => false,
                'message' => 'Usuario o Password incorrectos'
            ];
        }

    }

    public function logout()
    {
        Auth::logout();
        return[
            'success' => true,
            'message' => 'Logout Success'
        ];

    }

    public function storeUser(Request $request)
    {
        try{

         
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->establishment_id = 1;
            $user->type = 'client';
            $user->api_token = str_random(50);
            $user->password = bcrypt($request->input('password'));
            $user->save();
            $user->modules()->sync([10]);

            $credentials = [ 'email' => $user->email, 'password' => $request->input('password') ];
            Auth::attempt($credentials);

            return [
                'success' => true,
                'message' => 'Usuario registrado'
            ];

        }catch(Exception $e)
        {
            return [
                'success' => false,
                'message' =>  $e->getMessage()
            ];
        }
       
    }

    public function transactionFinally(Request $request)
    {
        try{
            $user = auth()->user();

            //1. confirmar dato de compriante en order
            $order_generated = Order::find($request->orderId);
            $order_generated->document_external_id = $request->document_external_id;
            $order_generated->number_document = $request->number_document;
            $order_generated->save();
            
            $user->update(['identity_document_type_id' => $request->identity_document_type_id, 'number'=>$request->number]);
            
            return [
                'success' => true,
                'message' => 'Order Actualizada'
            ];
        }
        catch(Exception $e)
        {
            return [
                'success' => false,
                'message' =>  $e->getMessage()
            ];
        }
      
    }

    public function paymentCash(Request $request)
    {
        try{

            $user = auth()->user();
            $order = Order::create([
                'external_id' => Str::uuid()->toString(),
                'customer' =>  $request->customer,
                'shipping_address' => 'direccion 1',
                'items' =>  $request->items,
                'total' => $request->precio_culqi,
                'reference_payment' => 'efectivo',
              ]);
      
            $customer_email = $user->email;
            $document = new stdClass;
            $document->client = $user->name;
            $document->product = $request->producto;
            $document->total = $request->precio_culqi;
            Mail::to($customer_email)->send(new CulqiEmail($document));
      
            return [
                'success' => true,
                'order' => $order
            ];

        }catch(Exception $e)
        {
            return [
                'success' => false,
                'message' =>  $e->getMessage()
            ];
        }
       
    }

    public function ratingItem(Request $request)
    {
        if(auth()->user())
        {
            $user_id = auth()->id();
            $row = ItemsRating::firstOrNew( ['user_id' => $user_id, 'item_id' => $request->item_id ] );
            $row->value = $request->value;
            $row->save();
            return[
                'success' => false,
                'message' => 'Rating Guardado'
            ];
        }
        return[
            'success' => false,
            'message' => 'No se guardo Rating'
        ];

    }

    public function getRating($id)
    {
        if(auth()->user())
        {
            $user_id = auth()->id();
            $row = ItemsRating::where('user_id', $user_id)->where('item_id', $id)->first();
            return[
                'success' => true,
                'value' => ($row) ? $row->value : 0,
                'message' => 'Valor Obtenido'
            ];
        }
        return[
            'success' => false,
            'value' => 0,
            'message' => 'No se obtuvo valor'
        ];

    }




   
      
}
