<?php

namespace App\Entity;

use App\Repository\BookAuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;

#[ORM\Entity(repositoryClass: BookAuthorRepository::class)]
#[ExclusionPolicy(ExclusionPolicy::NONE)]
class BookAuthor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class, orphanRemoval: true)]
    #[Exclude]
    private $books;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[Pure] public function __construct()
    {
        $this->books = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Collection
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
