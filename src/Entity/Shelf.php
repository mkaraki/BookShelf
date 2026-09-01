<?php

namespace App\Entity;

use App\Repository\ShelfRepository;
use App\Utils\InternalCodeUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShelfRepository::class)]
class Shelf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $shelfNumber = null;

    #[ORM\ManyToOne(inversedBy: 'shelves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BookCase $parentBookCase = null;

    /**
     * @var Collection<int, OwnedBook>
     */
    #[ORM\OneToMany(targetEntity: OwnedBook::class, mappedBy: 'parentShelf')]
    private Collection $ownedBooks;

    public function __construct()
    {
        $this->ownedBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShelfNumber(): ?int
    {
        return $this->shelfNumber;
    }

    public function setShelfNumber(int $shelfNumber): static
    {
        $this->shelfNumber = $shelfNumber;

        return $this;
    }

    public function getParentBookCase(): ?BookCase
    {
        return $this->parentBookCase;
    }

    public function setParentBookCase(?BookCase $parentBookCase): static
    {
        $this->parentBookCase = $parentBookCase;

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
            $ownedBook->setParentShelf($this);
        }

        return $this;
    }

    public function removeOwnedBook(OwnedBook $ownedBook): static
    {
        if ($this->ownedBooks->removeElement($ownedBook)) {
            // set the owning side to null (unless already changed)
            if ($ownedBook->getParentShelf() === $this) {
                $ownedBook->setParentShelf(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return InternalCodeUtil::generateCode(InternalCodeUtil::CODE_TYPE_SHELF, $this->id);
    }
}
