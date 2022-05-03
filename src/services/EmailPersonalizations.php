<?php

namespace App\services;

use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
abstract class EmailPersonalizations
{
    public function __construct(
        public array               $to,
        public DynamicTemplateData $dynamicTemplateData

    )
    {
    }
}