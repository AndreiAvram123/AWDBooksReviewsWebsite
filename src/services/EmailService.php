<?php

namespace App\services;

use App\Entity\BookReview;
use App\Entity\EmailValidation;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;

class EmailService
{
    private string $fromIdentity = "a.t.avram@edu.salford.ac.uk";
    private string $confirmationEmailTemplateID = "d-2c206b86f8014d88b5e1557ac0993550";
    private string $newReviewTemplate = "d-83e60e79c21942c1b04343c9b07ebb52";
    private Client $client;
    private const baseUrl = "https://api.sendgrid.com/v3/mail/send";
    private const reviewsUrl = "http://awd-books-review-website-dev.herokuapp.com/reviews";


    public function __construct(
        private string $sendGridApiKey,
        private SerializerInterface $serializer

    ){
        $this->client = new Client(['base_uri' => self::baseUrl]);
    }



    public function sendNewReviewEmail(
        BookReview $bookReview
    ){

       $subscribersEmail = array_map(function (User $user){
           return $user ->getEmail();
       },$bookReview->getCreator()->getSubscribers());

            $emailData = new EmailData(
                from: new EmailWrapper($this->fromIdentity),
                personalizations: new NewBookReviewPersonalizations(
                    to: $subscribersEmail,
                    dynamicTemplateData: new NewBookReviewTemplateData(
                        authorName: $bookReview->getCreator()->getUsername(),
                        reviewUrl: self::reviewsUrl . "/" . $bookReview->getId()
                    )
                ),
                templateID: $this->newReviewTemplate
            );
            $emailDataJson = $this->serializer->serialize($emailData, 'json');
            $this->client->post(self::baseUrl, [
                'headers' => [
                    'Authorization' => "Bearer " . $this->sendGridApiKey,
                    'Content-Type' => 'application/json'
                ],
                'body' => $emailDataJson
            ]);
    }
}