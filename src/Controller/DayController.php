<?php

namespace App\Controller;

use App\Entity\Day;
use App\Entity\Week;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/day')]
final class DayController extends AbstractController
{
  #[Route('/add', name: 'api_day_add', methods: ['POST'])]
  public function add(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $user = $this->getUser();
    if (!$user)
      return new JsonResponse(['error' => 'Non autorisé'], 401);

    if (empty($data['date']) || empty($data['weekId'])) {
      return new JsonResponse(['error' => 'Date ou semaine manquante'], 400);
    }

    $week = $entityManager->getRepository(Week::class)->find($data['weekId']);
    if (!$week)
      return new JsonResponse(['error' => 'Semaine introuvable'], 404);

    $day = new Day();
    $day->setDate(new \DateTime($data['date']));
    $day->setWeek($week);

    $entityManager->persist($day);
    $entityManager->flush();

    return new JsonResponse([
      'message' => 'Jour ajouté',
      'weekId' => $day->getWeek()->getId(),
      'dayId' => $day->getId(),
      'date' => $day->getDate()->format('Y-m-d'),
    ], 201);
  }

  #[Route('/{id}', name: 'api_day_update', methods: ['PUT'])]
  public function update(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
    $user = $this->getUser();
    if (!$user)
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    $day = $entityManager->getRepository(Day::class)->find($id);
    if (!$day)
      return new JsonResponse(['error' => 'Jour introuvable'], 404);
    $data = json_decode($request->getContent(), true);
    if (!isset($data['date']))
      return new JsonResponse(['error' => 'Date manquante'], 400);
    $day->setDate(new \DateTime($data['date']));
    $entityManager->flush();
    return new JsonResponse(['message' => 'Jour mis à jour']);
  }

  #[Route('/delete/{id}', name: 'api_day_delete', methods: ['DELETE'])]
  public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    $user = $this->getUser();
    if (!$user)
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    $day = $entityManager->getRepository(Day::class)->find($id);
    if (!$day)
      return new JsonResponse(['error' => 'Jour introuvable'], 404);
    $entityManager->remove($day);
    $entityManager->flush();
    return new JsonResponse(['message' => 'Jour supprimé']);
  }
}
