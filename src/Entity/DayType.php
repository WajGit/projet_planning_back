<?php

namespace App\Entity;

use App\Repository\DayTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DayTypeRepository::class)]
class DayType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'dayTypes')]
    private ?WeekType $week = null;

    /**
     * @var Collection<int, SlotType>
     */
    #[ORM\OneToMany(targetEntity: SlotType::class, mappedBy: 'day' , cascade: ['persist', 'remove'])]
    private Collection $slotTypes;

    public function __construct()
    {
        $this->slotTypes = new ArrayCollection();
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

    public function getWeek(): ?WeekType
    {
        return $this->week;
    }

    public function setWeek(?WeekType $week): static
    {
        $this->week = $week;

        return $this;
    }

    /**
     * @return Collection<int, SlotType>
     */
    public function getSlotTypes(): Collection
    {
        return $this->slotTypes;
    }

    public function addSlotType(SlotType $slotType): static
    {
        if (!$this->slotTypes->contains($slotType)) {
            $this->slotTypes->add($slotType);
            $slotType->setDay($this);
        }

        return $this;
    }

    public function removeSlotType(SlotType $slotType): static
    {
        if ($this->slotTypes->removeElement($slotType)) {
            // set the owning side to null (unless already changed)
            if ($slotType->getDay() === $this) {
                $slotType->setDay(null);
            }
        }

        return $this;
    }
}
