<?php

namespace App\services;

use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class NewBookReviewTemplateData extends DynamicTemplateData
{

    public function __construct(
        public string $authorName,
        public string $reviewUrl,
    )
    {
    }
}