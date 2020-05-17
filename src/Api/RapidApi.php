<?php

namespace Cvar1984\Api;

class RapidApi
{
    public function urban($word)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://mashape-community-urban-dictionary.p.rapidapi.com/define?term=$word",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: mashape-community-urban-dictionary.p.rapidapi.com",
                "x-rapidapi-key: 90e3d868bcmsh6dccbe44aaee05dp176632jsn89221b625f4b",
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}
