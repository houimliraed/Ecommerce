<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $shippingCost = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'city')]
    private Collection $createdAt;

    public function __construct()
    {
        $this->createdAt = new ArrayCollection();
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

    public function getShippingCost(): ?float
    {
        return $this->shippingCost;
    }

    public function setShippingCost(float $shippingCost): static
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getCreatedAt(): Collection
    {
        return $this->createdAt;
    }

    public function addCreatedAt(Order $createdAt): static
    {
        if (!$this->createdAt->contains($createdAt)) {
            $this->createdAt->add($createdAt);
            $createdAt->setCity($this);
        }

        return $this;
    }

    public function removeCreatedAt(Order $createdAt): static
    {
        if ($this->createdAt->removeElement($createdAt)) {
            // set the owning side to null (unless already changed)
            if ($createdAt->getCity() === $this) {
                $createdAt->setCity(null);
            }
        }

        return $this;
    }
}
