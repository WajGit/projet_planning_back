<?php

namespace App\Entity;

use App\Repository\WeekRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WeekRepository::class)]
class Week
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['company:read'])]
    #[ORM\Column]
    private ?int $year = null;

    #[Groups(['company:read'])]
    #[ORM\Column]
    private ?int $number = null;

    #[ORM\ManyToOne(inversedBy: 'weeks')]
    private ?Calendar $calendar = null;

    /**
     * @var Collection<int, Day>
     */
    #[ORM\OneToMany(mappedBy: 'week', targetEntity: Day::class, cascade: ['persist', 'remove'])]
    private Collection $days;

    public function __construct()
    {
        $this->days = new ArrayCollection();
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

    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    public function setCalendar(?Calendar $calendar): static
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * @return Collection<int, Day>
     */
    public function getDays(): Collection
    {
        return $this->days;
    }

    public function addDay(Day $day): static
    {
        if (!$this->days->contains($day)) {
            $this->days->add($day);
            $day->setWeek($this);
        }

        return $this;
    }

    public function removeDay(Day $day): static
    {
        if ($this->days->removeElement($day)) {
            // set the owning side to null (unless already changed)
            if ($day->getWeek() === $this) {
                $day->setWeek(null);
            }
        }

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }
}
