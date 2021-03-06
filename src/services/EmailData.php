<?php

namespace App\services;

use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
 class EmailData
{


    public function __construct(
        public EmailWrapper $from,
        public array $personalizations,
        public string $templateID
    )
    {

    }
}
