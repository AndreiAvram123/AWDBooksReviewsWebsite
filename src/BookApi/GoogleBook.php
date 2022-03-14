<?php

namespace App\BookApi;

use App\BookApi\VolumeInfo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Type;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class GoogleBook
{
   private string $id = "";
   private string $selfLink = "";

   #[Type("App\BookApi\VolumeInfo")]
   private ?VolumeInfo $volumeInfo = null;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSelfLink(): string
    {
        return $this->selfLink;
    }

    /**
     * @param string $selfLink
     */
    public function setSelfLink(string $selfLink): void
    {
        $this->selfLink = $selfLink;
    }

    /**
     * @return VolumeInfo
     */
    public function getVolumeInfo(): VolumeInfo
    {
        return $this->volumeInfo;
    }

    /**
     * @param VolumeInfo $volumeInfo
     */
    public function setVolumeInfo(VolumeInfo $volumeInfo): void
    {
        $this->volumeInfo = $volumeInfo;
    }


}
