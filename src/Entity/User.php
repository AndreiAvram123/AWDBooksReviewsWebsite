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
use function PHPUnit\Framework\isNull;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: 'username', message: "The username is already taken")]
#[UniqueEntity(fields: 'email', message: "The email is already taken")]
#[UniqueEntity(fields: 'nickname', message: "The nickname is already taken")]

class User implements UserInterface, PasswordAuthenticatedUserInterface, \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Length(min : 5, max: 20)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Length(min: 5, minMessage: "The password is too weak")]
    private ?string $password;

    #[ORM\Column(type : 'json')]
    private array $roles = array("ROLE_USER");

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: BookReview::class, orphanRemoval: true)]
    private $bookReviews;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nickname;

    #[ORM\OneToOne(inversedBy: 'owner', targetEntity: SocialMediaHub::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private $socialHub;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private ?Image $profileImage ;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: UserRating::class, orphanRemoval: true)]
    private $userRatings;



    #[Pure] public function __construct()
    {
        $this->bookReviews = new ArrayCollection();
        $this->userRatings = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProfileImage(): ?Image
    {
        if(is_null($this->profileImage )){
            $image = new Image();
            $image->setUrl("https://robohash.org/138.246.253.15.png");
            return $image;
        }
        return $this->profileImage;
    }

    public function setProfileImage(?Image $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * @return Collection|UserRating[]
     */
    public function getUserRatings(): Collection
    {
        return $this->userRatings;
    }

    public function addUserRating(UserRating $userRating): self
    {
        if (!$this->userRatings->contains($userRating)) {
            $this->userRatings[] = $userRating;
            $userRating->setCreator($this);
        }

        return $this;
    }

    public function removeUserRating(UserRating $userRating): self
    {
        if ($this->userRatings->removeElement($userRating)) {
            // set the owning side to null (unless already changed)
            if ($userRating->getCreator() === $this) {
                $userRating->setCreator(null);
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
       return [

       ];
    }
}
