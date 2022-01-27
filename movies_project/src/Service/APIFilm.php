<?php
namespace App\Service;


class APIFilm  {
    public static function search ($nom) 
    {
        $description = NULL;
        $apiKey = 'cc7474c1'; 
        $url = "http://www.omdbapi.com/?apikey=" . $apiKey . "&t=" . str_replace(" ", "&20", $nom);
        $response = file_get_contents($url);

        try
        {
            $description = json_decode($response,true)["Plot"];
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
        }

        return $description;
    }


}

?>