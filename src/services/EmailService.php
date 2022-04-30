<?php

namespace App\services;

use App\Entity\BookReview;
use App\Entity\EmailValidation;
use App\Entity\User;
use App\Repository\EmailValidationRepository;
use App\ResponseModels\ErrorWrapper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use SendGrid\Mail\Mail;

class EmailService
{
    private \SendGrid $sendGrid;
    private string $fromIdentity = "a.t.avram@edu.salford.ac.uk";
    private string $confirmationEmailTemplateID = "d-2c206b86f8014d88b5e1557ac0993550";
    private string $newReviewTemplate = "d-83e60e79c21942c1b04343c9b07ebb52";
    private Client $client;
    private const baseUrl = "https://api.sendgrid.com/v3/mail/send";
    private const reviewsUrl = "http://awd-books-review-website-dev.herokuapp.com/reviews";


    public function __construct(
        private string $sendGridApiKey,
        private string $verificationURL,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer

    ){
        $this->sendGrid = new \SendGrid($sendGridApiKey);
        $this->client = new Client(['base_uri' => self::baseUrl]);
    }



    public function sendNewReviewEmail(
        BookReview $bookReview
    ){

        foreach ($bookReview->getCreator()->getSubscribers() as $subscriber) {
            $emailData = new EmailData(
                from: new EmailWrapper($this->fromIdentity),
                personalizations: new NewBookReviewPersonalizations(
                    to: new EmailWrapper($subscriber->getEmail()),
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



    public function sendConfirmationEmail(User $user){
        $email = new Mail();
        $email->setFrom($this->fromIdentity);
        $email->setTemplateId($this->confirmationEmailTemplateID);
        $uuid = uniqid();
        $email->addDynamicTemplateData(
            key: "confirmationLink",
            value: $this->verificationURL . $uuid
        );
        $email->addTo(to : $user->getEmail());
        try{
            $response = $this->sendGrid->send($email);
            if($response->statusCode()> 200 && $response->statusCode() < 400){
                $this->persistEmailValidation(
                    uuid: $uuid,
                    user: $user
                );
            }
        }catch (Exception $exception){

        }
    }


    private function persistEmailValidation(
        string $uuid,
        User $user
    ):EmailValidation{
        $emailValidation = new EmailValidation();
        $emailValidation->setUser($user);
        $emailValidation->setUuid($uuid);
        $now = new \DateTimeImmutable();
        $expirationDate = $now->modify('+1 day');
        $emailValidation->setExpirationDate($expirationDate);
        $this->entityManager->persist($emailValidation);
        $this->entityManager->flush();
        return $emailValidation;
    }


    public function setEmailValidated(
        EmailValidation $emailValidation
    ){
        $emailValidation->getUser()->setIsEmailVerified(true);
        $this->entityManager->remove($emailValidation);
        $this->entityManager->flush();
    }

    public function removeExpiredVerification(
        EmailValidation $emailValidation
    ){
        $this->entityManager->remove($emailValidation);
        $this->entityManager->flush();
    }

}