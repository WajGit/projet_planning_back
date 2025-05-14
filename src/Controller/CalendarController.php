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
        'year' => $week->getYear(),
        'number' => $week->getNumber(),
        'days' => [],
      ];
      foreach ($week->getDays() as $day) {
        $dayData = [
          'id' => $day->getId(),
          'name' => $day->getName()->format('Y-m-d'),
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
    $groupId = $data['groupId'] ?? null;
    if (!$groupId) {
        return new JsonResponse(['error' => 'ID de groupe manquant'], 400);
    }
    $group = $entityManager->getRepository(Group::class)->find($groupId);
    if (!$group) {
        return new JsonResponse(['error' => 'Groupe introuvable'], 404);
    }
    if ($group->getCalendar()) {
        return new JsonResponse(['error' => 'Un calendrier existe déjà pour ce groupe'], 409);
    }
    // Création du calendrier
    $calendar = new Calendar();
    $calendar->setName('Planning de ' . $group->getName());
    $calendar->setCreatedAt(new \DateTimeImmutable());
    $calendar->setUser($user);
    $group->setCalendar($calendar);
    $entityManager->persist($calendar);
    $entityManager->flush();
    $data = [
      'id' => $calendar->getId(),
      'name' => $calendar->getName(),
      'weeks' => [],
    ];
    foreach ($calendar->getWeeks() as $week) {
      $weekData = [
        'id' => $week->getId(),
        'name' => $week->getName(),
        'year' => $week->getYear(),
        'number' => $week->getNumber(),
        'days' => [],
      ];
      foreach ($week->getDays() as $day) {
        $dayData = [
          'id' => $day->getId(),
          'name' => $day->getName()->format('Y-m-d'),
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
        // $day->setDate(new \DateTime($dayData['date']));
        $day->setName(new \DateTime($dayData['date']));
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



  #[Route('/generate', name: 'api_calendar_generate', methods: ['POST'])]
  public function generate(Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
      /* Lecture / vérifs de base */
      $data = json_decode($request->getContent(), true);
      $user = $this->getUser();
      if (!$user) {
          return new JsonResponse(['error' => 'Non autorisé'], 401);
      }

      $groupId   = $data['groupId'] ?? null;
      $startDate = isset($data['startDate']) ? new \DateTime($data['startDate']) : null;
      $endDate   = isset($data['endDate']) ? new \DateTime($data['endDate']) : null;

      if (!$groupId || !$startDate || !$endDate) {
          return new JsonResponse(['error' => 'Données manquantes'], 400);
      }

      $group = $entityManager->getRepository(Group::class)->find($groupId);
      if (!$group) {
          return new JsonResponse(['error' => 'Groupe introuvable'], 404);
      }

      $planningType = $group->getPlanningType();
      if (!$planningType) {
          return new JsonResponse(['error' => "Ce groupe n'a pas de trame associée"], 400);
      }

      /* Récupération ou création du Calendar */

      $calendar = $group->getCalendar();
      //tableau associatif avec année-numSemaine en clé -> vérifier rapidement si une semaine est déjà enregistrée
      $existingWeeks = [];
      //tableau pour stocker les semaines qu'on souhaite ajouter mais existent déjà en BD
      $skippedWeeks = [];

      if (!$calendar) {
          $calendar = new Calendar();
          $calendar->setName('Planning de ' . $group->getName());
          $calendar->setCreatedAt(new \DateTimeImmutable());
          $calendar->setUser($user);
          $group->setCalendar($calendar);
          $entityManager->persist($calendar);
      } else {
          foreach ($calendar->getWeeks() as $week) {
              $key = $week->getYear() . '-' . $week->getNumber();
              $existingWeeks[$key] = true;
          }
      }

      /* Boucle sur tout les jours que l'on souhaite générer */
      $currentDay = clone $startDate;
      $weeks   = [];
      $nbWeeksType = $planningType->getWeeks()->count();

      while ($currentDay <= $endDate) {
          // Il peux y avoir plusieurs semaines dans une trame
          // On cherche à savoir combien de jours se sont écoules pour savoir comment appliquer la trame
          $daysSinceStart = $startDate->diff($currentDay)->days;
          $idWeekType = intdiv($daysSinceStart, 7) % $nbWeeksType;
          $dayIndex = ((int) $currentDay->format('N')) - 1;
          $weeksType = $planningType->getWeeks()[$idWeekType] ?? null;
          if (!$weeksType) {
              $currentDay->modify('+1 day');
              continue;
          }
          $daysType = $weeksType->getDayTypes()[$dayIndex] ?? null;
          if (!$daysType) {
              $currentDay->modify('+1 day');
              continue;
          }
          // On trouve le lundi de la semaine courante
          $mondayDate = (clone $currentDay)->modify('monday this week');
          $weekYear   = (int) $mondayDate->format('o');
          $weekNumber = (int) $mondayDate->format('W');
          $weekKey    = $weekYear . '-' . $weekNumber;

          if (isset($existingWeeks[$weekKey])) {
              if (!in_array($weekKey, $skippedWeeks)) {
                $skippedWeeks[] = $weekKey;
              }
              $currentDay->modify('+1 day');
              continue;
          }

          if (!isset($weeks[$weekKey])) {
              $week = new Week();
              $week->setYear($weekYear);
              $week->setNumber($weekNumber);

              // Générer le nom : Mois ou Mois/Mois si chevauchement
              $weekDays = [];
              for ($i = 0; $i < 7; $i++) {
                  $date = (clone $mondayDate)->modify("+$i days");
                  $weekDays[] = ucfirst($date->format('F'));
              }
              $uniqueMonths = array_unique($weekDays);
              $weekName = implode('/', $uniqueMonths);

              $week->setName($weekName);
              $week->setCalendar($calendar);
              $weeks[$weekKey] = $week;
              $calendar->addWeek($week);
          }

          $week = $weeks[$weekKey];

          $day = new Day();
          $day->setName(clone $currentDay);
          $day->setWeek($week);
          $week->addDay($day);
          // Ajout des slots au jour en fonction de la trame
          foreach ($daysType->getSlotTypes() as $slotType) {
              $slot = new Slot();
              $slot->setStartTime(
                  new \DateTime($currentDay->format('Y-m-d') . ' ' . $slotType->getStartTime()->format('H:i'))
              );
              $slot->setEndTime(
                  new \DateTime($currentDay->format('Y-m-d') . ' ' . $slotType->getEndTime()->format('H:i'))
              );
              $slot->setColor($slotType->getColor());
              $slot->setDay($day);
              $day->addSlot($slot);
          }
          $currentDay->modify('+1 day');
      }

      $entityManager->flush();

      return new JsonResponse([
          'message'       => 'Planning généré avec succès',
          'calendarId'    => $calendar->getId(),
          'skippedWeeks'  => $skippedWeeks,
      ], 201);
  }


}