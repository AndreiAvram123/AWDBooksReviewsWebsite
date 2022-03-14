<?php

namespace App\BookApi;
use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class GoogleBookImages
{
   private string $smallThumbnail = "";
   private string $thumbnail = "";

    /**
     * @return string
     */
    public function getSmallThumbnail(): string
    {
        return $this->smallThumbnail;
    }

    /**
     * @param string $smallThumbnail
     */
    public function setSmallThumbnail(string $smallThumbnail): void
    {
        $this->smallThumbnail = $smallThumbnail;
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }



}