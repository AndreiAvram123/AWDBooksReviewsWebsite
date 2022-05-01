<?php

namespace App\Repository;

use App\BookApi\GoogleBookDTO;
use App\BookApi\GoogleBooksSearchResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

class GoogleBookApiRepository
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

   public function getVolumeById(string $volumeID):?GoogleBookDTO{
      $response =  $this->client->get("volumes/" . $volumeID,
      [
          'query' => [
              'key' => $this->apiKey
          ]
      ]);
       /**
        * @var  GoogleBookDTO $bookDto
        */
       return $this->serializer->deserialize(
           data : (string) $response->getBody(),
           type: GoogleBookDTO::class,format: 'json'
       );
   }

    /**
     * @param $title
     * @return GoogleBookDTO[]
     * @throws GuzzleException
     */
   public function searchByTitle($title):array{
       $response = $this->client->get(
          self::searchUrl,
           [
               'query' => [
                   'q'=> $title,
                   'key' => $this->apiKey
               ]
           ]
       );
       /**
        *  @var $serializedResponse GoogleBooksSearchResponse
        */
       $serializedResponse  = $this-> serializer->deserialize(
           data: (string)$response->getBody(),
           type: GoogleBooksSearchResponse::class, format: 'json'
       );
       return $serializedResponse->getItems();
   }

}