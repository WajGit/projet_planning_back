<?php

namespace App\Entity;

use App\Repository\RotationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RotationRepository::class)]
class Rotation
{
    #[Groups(['rotation:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['rotation:read'])]
    #[ORM\Column]
    private ?int $position = null;

    #[Groups(['rotation:read'])]
    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?PlanningType $planningType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getPlanningType(): ?PlanningType
    {
        return $this->planningType;
    }

    public function setPlanning(?PlanningType $planningType): static
    {
        $this->planningType = $planningType;

        return $this;
    }
}
