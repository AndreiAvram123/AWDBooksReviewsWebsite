<?php

namespace App\Entity;

use App\Repository\BookCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;

#[ORM\Entity(repositoryClass: BookCategoryRepository::class)]
#[ExclusionPolicy(ExclusionPolicy::NONE)]
class BookCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'categories')]
    private $books;

    #[ORM\ManyToMany(targetEntity: GoogleBook::class, mappedBy: 'categories')]
    private $googleBooks;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->googleBooks = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addCategory($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            $book->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection|GoogleBook[]
     */
    public function getGoogleBooks(): Collection
    {
        return $this->googleBooks;
    }

    public function addGoogleBook(GoogleBook $googleBook): self
    {
        if (!$this->googleBooks->contains($googleBook)) {
            $this->googleBooks[] = $googleBook;
            $googleBook->addCategory($this);
        }

        return $this;
    }

    public function removeGoogleBook(GoogleBook $googleBook): self
    {
        if ($this->googleBooks->removeElement($googleBook)) {
            $googleBook->removeCategory($this);
        }

        return $this;
    }


}
