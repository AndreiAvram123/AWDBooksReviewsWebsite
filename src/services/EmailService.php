<?php

namespace App\services;

use Exception;
use SendGrid\Mail\Mail;

class EmailService
{
    private \SendGrid $sendGrid;
    private string $fromIdentity = "a.t.avram@edu.salford.ac.uk";
    private string $confirmationEmailTemplateID = "d-2c206b86f8014d88b5e1557ac0993550";
   public function __construct(
       string $sendGridApiKey,
   ){
      $this->sendGrid = new \SendGrid($sendGridApiKey);
   }

   public function sendConfirmationEmail(string $to){
       $email = new Mail();
       $email->setFrom($this->fromIdentity);
       $email->setTemplateId($this->confirmationEmailTemplateID);
       $email->addTo(to : $to);
       try{
          $response = $this->sendGrid->send($email);
          print_r($response->statusCode());
       }catch (Exception $exception){

       }

   }
}