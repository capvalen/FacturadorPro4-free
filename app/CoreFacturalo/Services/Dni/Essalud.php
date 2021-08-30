<?php

namespace App\CoreFacturalo\Services\Dni;

use App\CoreFacturalo\Services\Helpers\Functions;
use App\CoreFacturalo\Services\Models\Person;
use GuzzleHttp\Client;

class Essalud
{
    public static function search($number)
    {
        if (strlen($number) !== 8) {
            return [
                'success' => false,
                'message' => 'DNI tiene 8 digitos.'
            ];
        }

        try {

            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL,"https://ww1.essalud.gob.pe/sisep/postulante/postulante/postulante_obtenerDatosPostulante.htm?strDni={$number}");
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $response = curl_exec ($ch);
            // curl_close ($ch);

            $client = new  Client(['base_uri' => 'https://ww1.essalud.gob.pe/sisep/postulante/postulante/']);
            $response = $client->request('GET', 'postulante_obtenerDatosPostulante.htm?strDni='.$number);
            if ($response->getStatusCode() == 200 && $response != "") {

                // if ($response) {

                $json = (object) json_decode($response->getBody()->getContents(), true);
                // $json = (object) json_decode($response, true);
                $data_person = $json->DatosPerson[0];
                if (isset($data_person) && count($data_person) > 0 &&
                    strlen($data_person['DNI']) >= 8 && $data_person['Nombres'] !== '') {
                    $person = new Person();
                    $person->name = $data_person['ApellidoPaterno'].' '.$data_person['ApellidoMaterno'].', '.$data_person['Nombres'];
                    $person->number = $data_person['DNI'];
                    $person->verification_code = Functions::verificationCode($data_person['DNI']);
                    $person->first_name = $data_person['ApellidoPaterno'];
                    $person->last_name = $data_person['ApellidoMaterno'];
                    $person->names = $data_person['Nombres'];
                    $person->date_of_birthday = $data_person['FechaNacimiento'];
                    $person->sex = ((string)$data_person['Sexo'] === '2')?'Masculino':'Femenino';
                    $person->voting_group = null;

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
            }

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return [
                'success' => false,
                'message' => 'Coneccion fallida.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Coneccion fallida.'
        ];
    }
}
?>
