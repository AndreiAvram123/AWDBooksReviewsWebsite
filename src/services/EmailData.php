<?php

namespace App\services;

use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
 class EmailData
{


    public function __construct(
        public EmailWrapper $from,
        public EmailPersonalizations $personalizations,
        public string $templateID
    )
    {

    }
}

#[ExclusionPolicy(ExclusionPolicy::NONE)]
abstract class EmailPersonalizations{
    public function __construct(
        public array $to,
        public DynamicTemplateData $dynamicTemplateData

    )
    {
    }
}

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class EmailWrapper{
    public function __construct(
        public string $email
    )
    {
    }
}
