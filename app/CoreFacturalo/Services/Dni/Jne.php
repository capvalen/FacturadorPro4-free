<?php

namespace App\CoreFacturalo\Services\Dni;

use App\CoreFacturalo\Services\Helpers\Functions;
use App\CoreFacturalo\Services\Models\Person;
use GuzzleHttp\Client;

class Jne
{
    public static function search($number)
    {
        if (strlen($number) !== 8) {
            return [
                'success' => false,
                'message' => 'DNI tiene 8 digitos.'
            ];
        }

        //nuevo metodo post
        $requestToken = 'Dmfiv1Unnsv8I9EoXEzbyQExSD8Q1UY7viyyf_347vRCfO-1xGFvDddaxDAlvm0cZ8XgAKTaWclVFnnsGgoy4aLlBGB5m-E8rGw_ymEcCig1:eq4At-H2zqgXPrPnoiDGFZH0Fdx5a-1UiyVaR4nQlCvYZzAhzmvWxLwkUk6-yORYrBBxEnoG5sm-Hkiyc91so6-nHHxIeLee5p700KE47Cw1';
        $url = 'https://aplicaciones007.jne.gob.pe/srop_publico/Consulta/api/AfiliadoApi/GetNombresCiudadano';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>json_encode(['CODDNI' => $number]),
            CURLOPT_HTTPHEADER => array(
                "Requestverificationtoken: ".$requestToken,
                "Authorization: Bearer ".$requestToken,
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response, true);

        // if ($response) {
        // $text = $response->getBody()->getContents();
        $text = $data['data'];
        $parts = explode('|', $text);

        if (count($parts) === 3) {

            $person = new Person();
            $person->number = $number;
            $person->verification_code = Functions::verificationCode($number);
            $person->name = $parts[0].' '.$parts[1].' '.$parts[2];
            $person->first_name = $parts[0];
            $person->last_name = $parts[1];
            $person->names = $parts[2];

            return [
                'success' => true,
                'data' => $person
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Datos no encontrados.'
            ];
        }
        // }

        return [
            'success' => false,
            'message' => 'Coneccion fallida.'
        ];
    }
}
