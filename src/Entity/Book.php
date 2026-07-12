<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bookRead = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Publisher $publisher = null;

    /**
     * @var Collection<int, Author>
     */
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    private Collection $authors;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    #[Assert\Isbn(
        type: Assert\Isbn::ISBN_13,
        message: 'This value is not valid ISBN-13 code.'
    )]
    private ?string $isbn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $disambiguation = null;

    /**
     * @var Collection<int, OwnedBook>
     */
    #[ORM\OneToMany(targetEntity: OwnedBook::class, mappedBy: 'book')]
    private Collection $ownedBooks;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->ownedBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBookRead(): ?string
    {
        return $this->bookRead;
    }

    public function setBookRead(?string $bookRead): static
    {
        $this->bookRead = $bookRead;

        return $this;
    }

    public function getPublisher(): ?Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(?Publisher $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getDisambiguation(): ?string
    {
        return $this->disambiguation;
    }

    public function setDisambiguation(?string $disambiguation): static
    {
        $this->disambiguation = $disambiguation;

        return $this;
    }

    /**
     * @return Collection<int, OwnedBook>
     */
    public function getOwnedBooks(): Collection
    {
        return $this->ownedBooks;
    }

    public function addOwnedBook(OwnedBook $ownedBook): static
    {
        if (!$this->ownedBooks->contains($ownedBook)) {
            $this->ownedBooks->add($ownedBook);
            $ownedBook->setBook($this);
        }

        return $this;
    }

    public function removeOwnedBook(OwnedBook $ownedBook): static
    {
        if ($this->ownedBooks->removeElement($ownedBook)) {
            // set the owning side to null (unless already changed)
            if ($ownedBook->getBook() === $this) {
                $ownedBook->setBook(null);
            }
        }

        return $this;
    }
}
