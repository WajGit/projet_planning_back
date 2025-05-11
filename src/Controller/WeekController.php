<?php

namespace App\Controller;

use App\Entity\Week;
use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/week')]
final class WeekController extends AbstractController
{
  #[Route('/add', name: 'api_week_add', methods: ['POST'])]
  public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $user = $this->getUser();
    if (!$user)
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    if (empty($data['name']) || empty($data['calendarId'])) {
      return new JsonResponse(['error' => 'Nom ou calendrier manquant'], 400);
    }
    $calendar = $entityManager->getRepository(Calendar::class)->find($data['calendarId']);
    if (!$calendar)
      return new JsonResponse(['error' => 'Calendrier introuvable'], 404);
    $week = new Week();
    $week->setName($data['name']);
    $week->setCalendar($calendar);
    $entityManager->persist($week);
    $entityManager->flush();
    return new JsonResponse(['message' => 'Semaine ajoutée', 'weekId' => $week->getId()], 201);
  }

  #[Route('/{id}', name: 'api_week_update', methods: ['PUT'])]
  public function update(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
    $user = $this->getUser();
    if (!$user)
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    $week = $entityManager->getRepository(Week::class)->find($id);
    if (!$week)
      return new JsonResponse(['error' => 'Semaine introuvable'], 404);
    $data = json_decode($request->getContent(), true);
    if (!isset($data['name']))
      return new JsonResponse(['error' => 'Nom manquant'], 400);
    $week->setName($data['name']);
    $entityManager->flush();
    return new JsonResponse(['message' => 'Semaine mise à jour']);
  }

  #[Route('/{id}', name: 'api_week_delete', methods: ['DELETE'])]
  public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    $user = $this->getUser();
    if (!$user)
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    $week = $entityManager->getRepository(Week::class)->find($id);
    if (!$week)
      return new JsonResponse(['error' => 'Semaine introuvable'], 404);
    $entityManager->remove($week);
    $entityManager->flush();
    return new JsonResponse(['message' => 'Semaine supprimée']);
  }
}
