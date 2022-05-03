<?php

namespace App\services;

use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class EmailWrapper
{
    public function __construct(
        public string $email
    )
    {
    }
}