<?php

namespace App\Entity;

use App\Repository\DayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DayRepository::class)]
class Day
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $name = null;

    #[ORM\ManyToOne(inversedBy: 'days')]
    private ?Week $week = null;

    /**
     * @var Collection<int, Slot>
     */
    #[ORM\OneToMany(mappedBy: 'day', targetEntity: Slot::class, cascade: ['persist', 'remove'])]
    private Collection $slots;

    public function __construct()
    {
        $this->slots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?\DateTimeInterface
    {
        return $this->name;
    }

    public function setName(\DateTimeInterface $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getWeek(): ?Week
    {
        return $this->week;
    }

    public function setWeek(?Week $week): static
    {
        $this->week = $week;

        return $this;
    }

    /**
     * @return Collection<int, Slot>
     */
    public function getSlots(): Collection
    {
        return $this->slots;
    }

    public function addSlot(Slot $slot): static
    {
        if (!$this->slots->contains($slot)) {
            $this->slots->add($slot);
            $slot->setDay($this);
        }

        return $this;
    }

    public function removeSlot(Slot $slot): static
    {
        if ($this->slots->removeElement($slot)) {
            // set the owning side to null (unless already changed)
            if ($slot->getDay() === $this) {
                $slot->setDay(null);
            }
        }

        return $this;
    }
}
