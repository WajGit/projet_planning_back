<?php

namespace App\Entity;

use App\Repository\PlanningTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlanningTypeRepository::class)]
class PlanningType
{
    #[Groups(['company:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'planningTypes')]
    private ?User $author = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'planningType', cascade: ['persist', 'remove'])]
    private Collection $groups;

    /**
     * @var Collection<int, WeekType>
     */
    #[ORM\OneToMany(targetEntity: WeekType::class, mappedBy: 'planningType', cascade: ['persist', 'remove'])]
    private Collection $weeks;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->groups = new ArrayCollection();
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

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
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->setPlanningType($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): static
    {
        if ($this->groups->removeElement($group)) {
            // set the owning side to null (unless already changed)
            if ($group->getPlanningType() === $this) {
                $group->setPlanningType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WeekType>
     */
    public function getWeeks(): Collection
    {
        return $this->weeks;
    }

    public function addWeek(WeekType $week): static
    {
        if (!$this->weeks->contains($week)) {
            $this->weeks->add($week);
            $week->setPlanningType($this);
        }

        return $this;
    }

    public function removeWeek(WeekType $week): static
    {
        if ($this->weeks->removeElement($week)) {
            // set the owning side to null (unless already changed)
            if ($week->getPlanningType() === $this) {
                $week->setPlanningType(null);
            }
        }

        return $this;
    }
}
