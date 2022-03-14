<?php

namespace App\Repository;

use App\BookApi\GoogleBooksSearchResponse;
use GuzzleHttp\Client;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

class GoogleBooksRepository
{
   private Client $client;
   private const baseUrl = "https://www.googleapis.com/books/v1/";
   private const searchUrl = self::baseUrl . "volumes";
    private SerializerInterface $serializer;
   public  function  __construct(
       private string $apiKey,

   )
   {

       $this->client = new Client(['base_uri' => self::baseUrl]);
       $this->serializer = SerializerBuilder::create()
           ->setPropertyNamingStrategy(
               new SerializedNameAnnotationStrategy(
                   new IdenticalPropertyNamingStrategy()
               )
           )
           ->build();
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

    /**
     * @param $title
     * @return GoogleBooksSearchResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
   public function searchByTitle($title):GoogleBooksSearchResponse{
       $response = $this->client->get(
          self::searchUrl,
           [
               'query' => [
                   'q'=> $title,
                   'key' => $this->apiKey
               ]
           ]
       );
       return  $this-> serializer->deserialize(
           data: (string)$response->getBody(),
           type: GoogleBooksSearchResponse::class, format: 'json'

       );
   }
}