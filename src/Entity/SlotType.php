<?php

namespace App\Entity;

use App\Repository\SlotTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: SlotTypeRepository::class)]
class SlotType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['slot:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['slot:read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'H:i'])]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['slot:read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'H:i'])]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(length: 255)]
    #[Groups(['slot:read'])]
    private ?string $color = null;

    #[ORM\ManyToOne(inversedBy: 'slotTypes')]
    private ?DayType $dayType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

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

    public function getDayType(): ?DayType
    {
        return $this->dayType;
    }

    public function setDayType(?DayType $dayType): static
    {
        $this->dayType = $dayType;

        return $this;
    }
}
