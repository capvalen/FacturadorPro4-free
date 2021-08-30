<?php

namespace App\Http\Middleware;

use Closure;

class RedirectModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $module = $request->user()->getModule();
        $path = explode('/', $request->path());
        $modules = $request->user()->getModules();

        if(!$request->ajax()){

            if(count($modules)){
                // if(count($modules) < 15){

                    $group = $this->getGroup($path, $module);

                    if($group){
                        if($this->getModuleByGroup($modules,$group) === 0){
                            return $this->redirectRoute($module);
                        }
                    }

                // }

            }
        }

        return $next($request);

    }


    private function redirectRoute($module){

        switch ($module) {

            case 'pos':
                return redirect()->route('tenant.pos.index');

            case 'documents':
                return redirect()->route('tenant.documents.create');

            case 'purchases':
                return redirect()->route('tenant.purchases.index');

            case 'advanced':
                return redirect()->route('tenant.retentions.index');

            case 'reports':
                return redirect()->route('tenant.reports.purchases.index');

            case 'configuration':
                return redirect()->route('tenant.companies.create');

            case 'inventory':
                return redirect()->route('warehouses.index');

            case 'accounting':
                return redirect()->route('tenant.account.index');

            case 'finance':
                return redirect()->route('tenant.finances.global_payments.index');

            case 'establishments':
                return redirect()->route('tenant.users.index');

            case 'hotels':
                return redirect()->url('/');

            case 'documentary-procedure':
                return redirect()->url('/');

            /*case 'ecommerce':
                return redirect()->route('tenant.ecommerce.index');*/

        }
    }



    private function getModuleByGroup($modules,$group){

        $modules_x_group  = $modules->filter(function ($module, $key) use($group){
            return $module->value === $group;
        });

        return $modules_x_group->count();
    }


    private function getGroup($path, $module){

        ///* Module Documents */

        if($path[0] == "documents"){
            $group = "documents";

        }
        elseif($path[0] == "dashboard"){
            $group = "documents";

        }
        elseif($path[0] == "quotations"){
            $group = "documents";

        }
        elseif($path[0] == "items"){
            $group = "documents";

        }
        elseif($path[0] == "summaries"){
            $group = "documents";

        }
        elseif($path[0] == "voided"){
            $group = "documents";

        }

        ///* Module purchases  */

        elseif($path[0] == "purchases"){
            $group = "purchases";

        }
        elseif($path[0] == "expenses"){
            $group = "purchases";

        }

        ///* Module advanced */

        elseif($path[0] == "retentions"){
            $group = "advanced";

        }
        elseif($path[0] == "dispatches"){
            $group = "advanced";

        }
        elseif($path[0] == "perceptions"){
            $group = "advanced";

        }

        ///* Module reports */

        elseif($path[0] == "list-reports"){
            $group = "reports";
        }
        elseif($path[0] == "reports" && $path[1] == "purchases"){
            $group = "reports";
        }
        elseif($path[0] == "reports" && $path[1] == "sales"){
            $group = "reports";
        }
        elseif($path[0] == "reports" && $path[1] == "consistency-documents"){
            $group = "reports";
        }

        // cuenta / listado de pagos
        elseif($path[0] == "cuenta"){
            $group = "cuenta";
        }

        ///* Module configuration */

        elseif($path[0] == "users"){
            $group = "establishments";
            // $group = "configuration";

        }
        elseif($path[0] == "establishments"){
            $group = "establishments";
            // $group = "configuration";

        }
        elseif($path[0] == "companies"){

            $group = "configuration";

            if(count($path) > 0 && $path[1] == "uploads" && $module == "documents"){
                $group = "documents";
            }

        }
        elseif($path[0] == "catalogs"){
            $group = "configuration";

        }
        elseif($path[0] == "advanced"){
            $group = "configuration";

        }

        ///* Determinate type person */

        elseif($path[0] == "persons"){
            if($path[1] == "suppliers"){
                $group = "purchases";

            }elseif($path[1] == "customers"){
                $group = "persons";

            }else{
                $group = null;
            }
        }
        elseif($path[0] == "person-types"){
            $group = "persons";

        }
        ///* Module pos */
        elseif($path[0] == "pos"){
            $group = "pos";

        }
        elseif($path[0] == "cash"){
            $group = "pos";

        }

        ///* Module inventory */
        elseif($path[0] == "warehouses"){
            $group = "inventory";
        }
        elseif($path[0] == "inventory"){
            $group = "inventory";
        }
        elseif($path[0] == "reports" && $path[1] == "kardex"){
            $group = "inventory";
        }
        elseif($path[0] == "reports" && $path[1] == "inventory"){
            $group = "inventory";
        }

        ///* Module accounting */
        elseif($path[0] == "account"){
            $group = "accounting";
        }

        ///* Module finance */
        elseif($path[0] == "finances"){
            $group = "finance";
        }

        elseif($path[0] == "orders"){
             $group = "ecommerce";

        }
        elseif($path[0] == "ecommerce" && $path[1] == "configuration"){
            $group = "ecommerce";
        }
        elseif($path[0] == "items_ecommerce"){
            $group = "ecommerce";
        }
        elseif($path[0] == "tags"){
            $group = "ecommerce";
        }
        elseif($path[0] == "promotions"){
            $group = "ecommerce";
        }
        elseif($path[0] == "hotels"){
            $group = "hotels";
        }
        elseif($path[0] == "documentary-procedure"){
            $group = "documentary-procedure";
        }

        else{
            $group = null;
        }

        return $group;
    }

}
