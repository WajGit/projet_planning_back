<?php

namespace App\Entity;

use App\Repository\WeekTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeekTypeRepository::class)]
class WeekType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'weeks')]
    private ?PlanningType $planningType = null;

    /**
     * @var Collection<int, DayType>
     */
    #[ORM\OneToMany(targetEntity: DayType::class, mappedBy: 'week', cascade: ['persist', 'remove'])]
    private Collection $dayTypes;

    public function __construct()
    {
        $this->dayTypes = new ArrayCollection();
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

    public function getPlanningType(): ?PlanningType
    {
        return $this->planningType;
    }

    public function setPlanningType(?PlanningType $planningType): static
    {
        $this->planningType = $planningType;

        return $this;
    }

    /**
     * @return Collection<int, DayType>
     */
    public function getDayTypes(): Collection
    {
        return $this->dayTypes;
    }

    public function addDayType(DayType $dayType): static
    {
        if (!$this->dayTypes->contains($dayType)) {
            $this->dayTypes->add($dayType);
            $dayType->setWeek($this);
        }

        return $this;
    }

    public function removeDayType(DayType $dayType): static
    {
        if ($this->dayTypes->removeElement($dayType)) {
            // set the owning side to null (unless already changed)
            if ($dayType->getWeek() === $this) {
                $dayType->setWeek(null);
            }
        }

        return $this;
    }
}
