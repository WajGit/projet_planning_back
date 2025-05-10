<?php

namespace App\Controller;

use App\Entity\Rotation;
use App\Entity\PlanningType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/rotation')]
final class RotationController extends AbstractController
{
  #[Route('/{planningTypeId}', name: 'rotation_list', methods: ['GET'])]
  public function getByPlanning(int $planningTypeId, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
  {
    $rotations = $entityManager->getRepository(Rotation::class)->findBy(
      ['planningType' => $planningTypeId],
      ['position' => 'ASC']
    );
    return $this->json([
      'message' => 'Affichage fait avec succès.',
      'planningId' => $planningTypeId,
      'rotation' => json_decode($serializer->serialize($rotations, 'json', ['groups' => 'rotation:read']))
    ]);
  }


  #[Route('/add', name: 'rotation_create', methods: ['POST'])]
  public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $planningId = $data['planningId'] ?? null;
    $color = $data['color'] ?? null;
    $position = $data['position'] ?? null;
    if (!$planningId || !$color || !$position) {
      return $this->json(['error' => 'Invalid data'], 400);
    }
    $rotation = new Rotation();
    $rotation->setColor($color);
    $rotation->setPosition($position);
    $rotation->setPlanning($entityManager->getReference(PlanningType::class, $planningId));
    $entityManager->persist($rotation);
    $entityManager->flush();
    return $this->json([
      'message' => 'Rotation ajoutée avec succès.',
      'planningId' => $rotation->getPlanningType()->getId(),
      'rotation' => $serializer->normalize($rotation, null, ['groups' => ['rotation:read']]),
    ]);
  }


  #[Route('/update/{id}', name: 'rotation_update', methods: ['PUT'])]
  public function update(int $id, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
  {
    $rotation = $entityManager->getRepository(Rotation::class)->find($id);
    if (!$rotation) {
      return $this->json(['error' => 'Rotation not found'], 404);
    }
    $data = json_decode($request->getContent(), true);
    $rotation->setPosition($data['position'] ?? $rotation->getPosition());
    $entityManager->flush();
    return $this->json([
      'message' => 'Rotation mise à jour avec succès.',
      'planningId' => $rotation->getPlanningType()->getId(),
      'rotation' => $serializer->normalize($rotation, null, ['groups' => ['rotation:read']]),
    ]);
  }


  #[Route('/delete/{id}', name: 'rotation_delete', methods: ['DELETE'])]
  public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    $rotation = $entityManager->getRepository(Rotation::class)->find($id);
    if (!$rotation) {
      return $this->json(['error' => 'Rotation not found'], 404);
    }
    $entityManager->remove($rotation);
    $entityManager->flush();
    return $this->json(['message' => 'Suppression effectuée',]);
  }
}
