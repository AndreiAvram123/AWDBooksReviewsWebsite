<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Unique;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: 'username', message: "The username is already taken")]
#[UniqueEntity(fields: 'email', message: "The email is already taken")]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Length(min : 5, max: 20)]
    private $username;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Length(min: 5, minMessage: "The password is too weak")]
    private $password;

    #[ORM\Column(type : 'json')]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: BookReview::class, orphanRemoval: true)]
    private $bookReviews;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $nickname;

    #[ORM\OneToOne(inversedBy: 'owner', targetEntity: SocialMediaHub::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private $socialHub;


    #[Pure] public function __construct()
    {
        $this->bookReviews = new ArrayCollection();
    }

    public function setRoles($roles){
        $this->roles= $roles;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): string
    {
       return "1234567";
    }

    public function eraseCredentials()
    {
        //no impl
    }

    public function __call(string $name, array $arguments)
    {
       return $this->username;
    }

    /**
     * @return Collection|BookReview[]
     */
    public function getBookReviews(): Collection
    {
        return $this->bookReviews;
    }

    public function addBookReview(BookReview $bookReview): self
    {
        if (!$this->bookReviews->contains($bookReview)) {
            $this->bookReviews[] = $bookReview;
            $bookReview->setCreator($this);
        }

        return $this;
    }

    public function removeBookReview(BookReview $bookReview): self
    {
        if ($this->bookReviews->removeElement($bookReview)) {
            // set the owning side to null (unless already changed)
            if ($bookReview->getCreator() === $this) {
                $bookReview->setCreator(null);
            }
        }

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getSocialHub(): ?SocialMediaHub
    {
        return $this->socialHub;
    }

    public function setSocialHub(SocialMediaHub $socialHub): self
    {
        $this->socialHub = $socialHub;

        return $this;
    }


}
