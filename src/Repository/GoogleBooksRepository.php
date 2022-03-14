<?php

namespace App\Repository;

use GuzzleHttp\Client;

class GoogleBooksRepository
{
   private Client $client;
   private const baseUrl = "https://www.googleapis.com/books/v1/";
   private const searchUrl = self::baseUrl . "volumes";

   public  function  __construct(private string $apiKey)
   {
       $this->client = new Client(['base_uri' => self::baseUrl]);
   }

   public function getVolumeById(string $volumeID):string{
      $response =  $this->client->get("volumes/" . $volumeID,
      [
          'query' => [
              'key' => $this->apiKey
          ]
      ]);
      return (string)$response->getBody();
   }

   public function searchByTitle($title):string{
       $response = $this->client->get(
          self::searchUrl,
           [
               'query' => [
                   'q'=> $title,
                   'key' => $this->apiKey
               ]
           ]
       );
       return (string)$response->getBody();
   }
}