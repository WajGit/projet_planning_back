<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'calendars')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'calendar')]
    private Collection $group;

    /**
     * @var Collection<int, Week>
     */
    #[ORM\OneToMany(mappedBy: 'calendar', targetEntity: Week::class, cascade: ['persist', 'remove'])]
    private Collection $weeks;

    public function __construct()
    {
        $this->group = new ArrayCollection();
        $this->weeks = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getgroup(): Collection
    {
        return $this->group;
    }

    public function addgroup(Group $group): static
    {
        if (!$this->group->contains($group)) {
            $this->group->add($group);
            $group->setCalendar($this);
        }

        return $this;
    }

    public function removegroup(group $group): static
    {
        if ($this->group->removeElement($group)) {
            // set the owning side to null (unless already changed)
            if ($group->getCalendar() === $this) {
                $group->setCalendar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Week>
     */
    public function getWeeks(): Collection
    {
        return $this->weeks;
    }

    public function addWeek(Week $week): static
    {
        if (!$this->weeks->contains($week)) {
            $this->weeks->add($week);
            $week->setCalendar($this);
        }

        return $this;
    }

    public function removeWeek(Week $week): static
    {
        if ($this->weeks->removeElement($week)) {
            // set the owning side to null (unless already changed)
            if ($week->getCalendar() === $this) {
                $week->setCalendar(null);
            }
        }

        return $this;
    }
}
