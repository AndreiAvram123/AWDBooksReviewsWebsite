<?php

namespace App\services;

use App\Entity\EmailValidation;
use App\Entity\User;
use App\Repository\EmailValidationRepository;
use App\ResponseModels\ErrorResponse;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SendGrid\Mail\Mail;

class EmailService
{
    private \SendGrid $sendGrid;
    private string $fromIdentity = "a.t.avram@edu.salford.ac.uk";
    private string $confirmationEmailTemplateID = "d-2c206b86f8014d88b5e1557ac0993550";
   public function __construct(
       string $sendGridApiKey,
       private string $verificationURL,
       private EntityManagerInterface $entityManager
   ){
      $this->sendGrid = new \SendGrid($sendGridApiKey);
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