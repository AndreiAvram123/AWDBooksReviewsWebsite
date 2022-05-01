<?php

namespace App\ResponseModels;

use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class ErrorWrapper
{
    private string $error = "";

    /**
     * @param string $error
     */
    public function __construct(string $error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }


}