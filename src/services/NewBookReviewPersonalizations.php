<?php

namespace App\services;

use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class NewBookReviewPersonalizations extends EmailPersonalizations
{

    public function __construct(
          EmailWrapper $to,
         public NewBookReviewTemplateData $dynamicTemplateData
   ){
       parent::__construct(to:  array($to));
   }
}
