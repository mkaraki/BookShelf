<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PrivateTrait
{
    #[ORM\Column(options: ['default' => false])]
    private bool $private = false;

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): static
    {
        $this->private = $private;

        return $this;
    }
}
