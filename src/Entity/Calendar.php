<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[Groups(['group:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['group:read'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['group:read'])]
    #[ORM\ManyToOne(inversedBy: 'calendars')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'calendar')]
    private ?Group $group = null;

    /**
     * @var Collection<int, Week>
     */
    #[Groups(['group:read'])]
    #[ORM\OneToMany(targetEntity: Week::class, mappedBy: 'calendar', cascade: ['persist', 'remove'])]
    private Collection $weeks;

    public function __construct()
    {
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

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): static
    {
        $this->group = $group;
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
            if ($week->getCalendar() === $this) {
                $week->setCalendar(null);
            }
        }
        return $this;
    }
}