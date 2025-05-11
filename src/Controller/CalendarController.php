<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Week;
use App\Entity\Day;
use App\Entity\Slot;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/calendar')]
final class CalendarController extends AbstractController
{
  #[Route('/{id}', name: 'api_calendar_show', methods: ['GET'])]
  public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    }
    $calendar = $entityManager->getRepository(Calendar::class)->find($id);
    if (!$calendar) {
      return new JsonResponse(['error' => 'Calendrier introuvable'], 404);
    }
    if ($calendar->getUser() !== $user) {
      return new JsonResponse(['error' => 'Accès refusé'], 403);
    }
    $data = [
      'id' => $calendar->getId(),
      'name' => $calendar->getName(),
      'weeks' => [],
    ];
    foreach ($calendar->getWeeks() as $week) {
      $weekData = [
        'id' => $week->getId(),
        'name' => $week->getName(),
        'days' => [],
      ];
      foreach ($week->getDays() as $day) {
        $dayData = [
          'id' => $day->getId(),
          'date' => $day->getDate()->format('Y-m-d'),
          'slots' => [],
        ];
        foreach ($day->getSlots() as $slot) {
          $dayData['slots'][] = [
            'id' => $slot->getId(),
            'startTime' => $slot->getStartTime()->format('H:i'),
            'endTime' => $slot->getEndTime()->format('H:i'),
            'color' => $slot->getColor(),
            'employee' => $slot->getEmployee()?->getId()
          ];
        }
        $weekData['days'][] = $dayData;
      }
      $data['weeks'][] = $weekData;
    }
    return new JsonResponse($data);
  }

  #[Route('/create', name: 'api_calendar_create', methods: ['POST'])]
  public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    }
    $name = $data['name'] ?? null;
    $groupId = $data['groupId'] ?? null;
    if (!$name || !$groupId) {
      return new JsonResponse(['error' => 'Nom ou groupe manquant'], 400);
    }
    $group = $entityManager->getRepository(Group::class)->find($groupId);
    if (!$group) {
      return new JsonResponse(['error' => 'Groupe introuvable'], 404);
    }
    if ($group->getCalendar()) {
      return new JsonResponse(['error' => 'Ce groupe a déjà un calendrier'], 409);
    }
    $calendar = new Calendar();
    $calendar->setName($name);
    $calendar->setCreatedAt(new \DateTimeImmutable());
    $calendar->setUser($user);
    $group->setCalendar($calendar);
    $entityManager->persist($calendar);
    $entityManager->flush();
    return $this->json([
      'id' => $calendar->getId(),
      'name' => $calendar->getName()
    ], 201);
  }

  #[Route('/save', name: 'api_calendar_save', methods: ['POST'])]
  public function save(Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $user = $this->getUser();
    if (!$user)
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    if (empty($data['name']) || empty($data['weeks'])) {
      return new JsonResponse(['error' => 'Nom ou semaines manquantes'], 400);
    }
    $calendar = new Calendar();
    $calendar->setName($data['name']);
    $calendar->setCreatedAt(new \DateTimeImmutable());
    $calendar->setUser($user);
    foreach ($data['weeks'] as $weekData) {
      $week = new Week();
      $week->setName($weekData['name']);
      $week->setCalendar($calendar);
      foreach ($weekData['days'] as $dayData) {
        $day = new Day();
        $day->setDate(new \DateTime($dayData['date']));
        $day->setWeek($week);
        foreach ($dayData['slots'] as $slotData) {
          $slot = new Slot();
          $slot->setStartTime(new \DateTime($slotData['startTime']));
          $slot->setEndTime(new \DateTime($slotData['endTime']));
          $slot->setColor($slotData['color']);
          $slot->setDay($day);
          // Si un employé est défini
          if (!empty($slotData['employeeId'])) {
            $entityManagerployee = $entityManager->getRepository(\App\Entity\Employee::class)->find($slotData['employeeId']);
            if ($entityManagerployee) {
              $slot->setEmployee($entityManagerployee);
            }
          }
          $day->addSlot($slot);
        }
        $week->addDay($day);
      }
      $calendar->addWeek($week);
    }
    $entityManager->persist($calendar);
    $entityManager->flush();
    return new JsonResponse([
      'message' => 'Calendrier enregistré avec succès',
      'calendarId' => $calendar->getId()
    ], Response::HTTP_CREATED);
  }
}
