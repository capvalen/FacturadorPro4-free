<?php

namespace App\CoreFacturalo\Services\Extras;

use Carbon\Carbon;
use GuzzleHttp\Client;
use DiDom\Document as DiDom;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Exception;

class ExchangeRate
{
    protected $client;

    public function __construct()
    {

    }

    private function search($month, $year)
    {

        try {

            // $url = "https://e-consulta.sunat.gob.pe/cl-at-ittipcam/tcS01Alias?mes={$month}&anho={$year}";
            // $url = 'http://www.sunat.gob.pe/a/txt/tipoCambio.txt';
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL,$url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // $response = curl_exec ($ch);
            // curl_close ($ch);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://www.sunat.gob.pe/a/txt/tipoCambio.txt',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);


            if ($response != "") {
                $html = $response;

                $explode = explode('|', $html);

                $values[] = [
                    (int)substr($html,0,2),
                    $explode[1],
                    $explode[2]
                ];

                // dd($response, $explode, $values);

                return collect($values)->toArray();


                // $xp = new DiDom($html);
                // $sub_headings = $xp->find('form table');
                // $trs = $sub_headings[1]->find('tr');
                // $values = [];

                // for($i = 1; $i < count($trs); $i++)
                // {
                //     $tr = $trs[$i];
                //     $tds = $tr->find('td');

                //     foreach($tds as $td)
                //     {
                //         $values[] = trim(preg_replace("/[\t|\n|\r]+/", '', $td->text()));
                //     }
                // }

                // return collect($values)->chunk(3)->toArray();
            }

        } catch (Exception $e) {
            // dd($e);
            Log::info("Error consulta T/C: ".$e->getMessage());
            return false;

        }

        return false;
    }

    public function searchDate($date)
    {
        // $date = Carbon::parse($date);
        // do {
        //     $res = $this->searchByDay($date);
        //     $date = $date->addDay(-1);
        // } while (!$res);

        // return $res;

        $date = Carbon::parse($date);

        $res = $this->searchByDay($date);
        // dd($res);

        $date = $date->addDay(-1);

        if(!$res){
            $res = $this->searchByDay($date);
            $date = $date->addDay(-1);
        }

        if(!$res){
            $res = $this->searchByDay($date);
            $date = $date->addDay(-1);
        }

        if(!$res){
            $res = $this->searchByDay($date);
            $date = $date->addDay(-1);
        }

        return $res;
    }

    private function searchByDay($date)
    {
        $day = $date->day;
        $year = $date->year;
        $month = $date->month;
        $exchange_rate = new  ExchangeRate();
        $exchange_rates = $exchange_rate->search($month, $year);
        if($exchange_rates) {
            foreach ($exchange_rates as $row)
            {
                $new_row = array_values($row);

                if ($new_row[0] == (int)$day) {
                    return [
                        'date_data' => $date->format('Y-m-d'),
                        'data' => [
                            'purchase' => $new_row[1],
                            'sale' => $new_row[2]
                        ]
                    ];
                }
            }
        }

        return false;
    }
}
