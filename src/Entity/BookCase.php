<?php

namespace App\Entity;

use App\Repository\BookCaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookCaseRepository::class)]
class BookCase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'bookCases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $parentRoom = null;

    /**
     * @var Collection<int, Shelf>
     */
    #[ORM\OneToMany(targetEntity: Shelf::class, mappedBy: 'parentBookCase')]
    private Collection $shelves;

    public function __construct()
    {
        $this->shelves = new ArrayCollection();
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

    public function getParentRoom(): ?Room
    {
        return $this->parentRoom;
    }

    public function setParentRoom(?Room $parentRoom): static
    {
        $this->parentRoom = $parentRoom;

        return $this;
    }

    /**
     * @return Collection<int, Shelf>
     */
    public function getShelves(): Collection
    {
        return $this->shelves;
    }

    public function addShelf(Shelf $shelf): static
    {
        if (!$this->shelves->contains($shelf)) {
            $this->shelves->add($shelf);
            $shelf->setParentBookCase($this);
        }

        return $this;
    }

    public function removeShelf(Shelf $shelf): static
    {
        if ($this->shelves->removeElement($shelf)) {
            // set the owning side to null (unless already changed)
            if ($shelf->getParentBookCase() === $this) {
                $shelf->setParentBookCase(null);
            }
        }

        return $this;
    }
}
