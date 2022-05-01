<?php

namespace App\services;

use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class NewBookReviewPersonalizations extends EmailPersonalizations
{

    public function __construct(
         array $to,
         NewBookReviewTemplateData $dynamicTemplateData
   ){
       parent::__construct(
           to:  $to,
           dynamicTemplateData: $dynamicTemplateData
       );
   }
}
