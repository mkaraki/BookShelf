<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use App\Utils\InternalCodeUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $roomFloor = null;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $parentSite = null;

    /**
     * @var Collection<int, BookCase>
     */
    #[ORM\OneToMany(targetEntity: BookCase::class, mappedBy: 'parentRoom')]
    private Collection $bookCases;

    public function __construct()
    {
        $this->bookCases = new ArrayCollection();
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

    public function getRoomFloor(): ?int
    {
        return $this->roomFloor;
    }

    public function setRoomFloor(?int $roomFloor): static
    {
        $this->roomFloor = $roomFloor;

        return $this;
    }

    public function getParentSite(): ?Site
    {
        return $this->parentSite;
    }

    public function setParentSite(?Site $parentSite): static
    {
        $this->parentSite = $parentSite;

        return $this;
    }

    /**
     * @return Collection<int, BookCase>
     */
    public function getBookCases(): Collection
    {
        return $this->bookCases;
    }

    public function addBookCase(BookCase $bookCase): static
    {
        if (!$this->bookCases->contains($bookCase)) {
            $this->bookCases->add($bookCase);
            $bookCase->setParentRoom($this);
        }

        return $this;
    }

    public function removeBookCase(BookCase $bookCase): static
    {
        if ($this->bookCases->removeElement($bookCase)) {
            // set the owning side to null (unless already changed)
            if ($bookCase->getParentRoom() === $this) {
                $bookCase->setParentRoom(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return InternalCodeUtil::generateCode(InternalCodeUtil::CODE_TYPE_ROOM, $this->id);
    }
}
