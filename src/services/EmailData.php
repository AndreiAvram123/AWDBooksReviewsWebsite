<?php

namespace App\services;

use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
 class EmailData
{

    public array $personalizations;
    public function __construct(
        public EmailWrapper $from,
        EmailPersonalizations $personalizations,
        public string $templateID
    )
    {
        $this->personalizations[] = $personalizations;
    }
}

#[ExclusionPolicy(ExclusionPolicy::NONE)]
abstract class EmailPersonalizations{
    public function __construct(
        public array $to
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

class EmailContent{
    public function  __construct(
        public string $type = "text/plain",
        public string $value
    )
    {
    }
}
