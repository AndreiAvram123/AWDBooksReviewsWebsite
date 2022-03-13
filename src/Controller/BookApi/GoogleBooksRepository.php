<?php

namespace App\Controller\BookApi;

use GuzzleHttp\Client;

class GoogleBooksRepository
{
   private Client $client;
   private const baseUrl = "https://www.googleapis.com/books/v1/ ";

   public  function  __construct()
   {
       $this->client = new Client(['base_uri' => self::baseUrl]);
   }

   public function getVolumeById(){
      $response =  $this->client->get("volumes/buc0AAAAMAAJ",
      [
          'query' => [
              'key' => 'AIzaSyCtUGoOZP4AzPlrD8fGDM_a3dqmh4GIwu8'
          ]
      ]);
      return (string)$response->getBody();
   }
}