<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            

            $client = new \GuzzleHttp\Client();
            $data = json_decode('{"request": {
                "slice": [
                  {
                    "origin": "MAD",
                    "destination": "BCN",
                    "date": "2017-09-20"
                  }
                ],
                "passengers": {
                  "adultCount": 1,
                  "infantInLapCount": 0,
                  "infantInSeatCount": 0,
                  "childCount": 0,
                  "seniorCount": 0
                },
                "solutions": 20,
                "refundable": false
              }}');
            /*$data = array();
            $data['request'] = array(
                'slice' => array(
                    'origin' => 'MAD',
                    'destination' => 'BCN',
                    'date' => '2017-09-20'
                ),
                'passengers' => array(
                    'adultCount' => 1,
                    'infantInLapCount' => 0,
                    'infantInSeatCount' => 0,
                    'childCount' => 0,
                    'seniorCount' => 0,
                ),
                'solutions' =>20,
                'refundable' => false
            );*/
            $params['body'] = json_encode($data);
            $params['headers'] =  [
                'Content-Type' => 'application/json',
            ];

            
            $res = $client->request(
                'POST', 
                'https://www.googleapis.com/qpxExpress/v1/trips/search?key=AIzaSyB8_xAlXwiXf_PamVBzkBXO2RgvQeY3chA',
                $params
            );

            $return = [
                'results' => json_decode((string) $res->getBody(), true),
                ];

        } catch (RequestException $e) {

            $error['message'] = $e->getMessage();

            if ($e->getResponse()) {
                $error['code'] = $e->getResponse()->getStatusCode();
            }

                var_dump(json_decode((string) $e->getMessage(), true));
            $return = [
                'error'   => $error,
                'results' => false,
            ];

        }
        dd($return);
        return view('home');
    }
}
