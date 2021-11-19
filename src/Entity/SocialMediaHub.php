<?php

namespace App\Entity;

use App\Repository\SocialMediaHubRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialMediaHubRepository::class)]
class SocialMediaHub
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $facebookURL;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $instagramURL;

    #[ORM\OneToOne(mappedBy: 'socialHub', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $owner;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $linkedIn;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFacebookURL()
    {
        return $this->facebookURL;
    }

    /**
     * @param mixed $facebookURL
     */
    public function setFacebookURL($facebookURL): void
    {
        $this->facebookURL = $facebookURL;
    }

    /**
     * @return mixed
     */
    public function getInstagramURL()
    {
        return $this->instagramURL;
    }

    /**
     * @param mixed $instagramURL
     */
    public function setInstagramURL($instagramURL): void
    {
        $this->instagramURL = $instagramURL;
    }



    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        // set the owning side of the relation if necessary
        if ($owner->getSocialHub() !== $this) {
            $owner->setSocialHub($this);
        }

        $this->owner = $owner;

        return $this;
    }

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(?string $linkedIn): self
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }
}
