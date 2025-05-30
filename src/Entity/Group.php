<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['company:read', 'employee:read'])]
  private ?int $id = null;

  #[Groups(['company:read', 'employee:read'])]
  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[Groups(['company:read', 'employee:read'])]
  #[ORM\Column(type: Types::TIME_MUTABLE)]
  private ?\DateTimeInterface $start = null;

  #[Groups(['company:read', 'employee:read'])]
  #[ORM\Column(type: Types::TIME_MUTABLE)]
  private ?\DateTimeInterface $end = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\ManyToOne(inversedBy: 'groups')]
  #[Groups(['group:read'])]
  private ?Company $company = null;

  #[Groups(['company:read'])]
  #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
  #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
  private ?PlanningType $planningType = null;

  #[Groups(['group:read'])]
  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
  private ?Calendar $calendar = null;


  public function __construct()
  {
    $this->createdAt = new \DateTimeImmutable();

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

  public function getStart(): ?\DateTimeInterface
  {
    return $this->start;
  }

  public function setStart(\DateTimeInterface $start): static
  {
    $this->start = $start;

    return $this;
  }

  public function getEnd(): ?\DateTimeInterface
  {
    return $this->end;
  }

  public function setEnd(\DateTimeInterface $end): static
  {
    $this->end = $end;

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

  public function getCompany(): ?Company
  {
    return $this->company;
  }

  public function setCompany(?Company $company): static
  {
    $this->company = $company;

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

  public function getCalendar(): ?Calendar
  {
      return $this->calendar;
  }

  public function setCalendar(?Calendar $calendar): static
  {
      $this->calendar = $calendar;

      return $this;
  }
}
