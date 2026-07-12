<?php

namespace App\Entity;

use App\Repository\OwnedBookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OwnedBookRepository::class)]
class OwnedBook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ownedBooks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shelf $parentShelf = null;

    #[ORM\ManyToOne(inversedBy: 'ownedBooks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentShelf(): ?Shelf
    {
        return $this->parentShelf;
    }

    public function setParentShelf(?Shelf $parentShelf): static
    {
        $this->parentShelf = $parentShelf;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }
}
