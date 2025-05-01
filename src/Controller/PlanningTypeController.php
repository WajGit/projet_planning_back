<?php

namespace App\Controller;

use App\Entity\PlanningType;
use App\Entity\WeekType;
use App\Entity\DayType;
use App\Entity\SlotType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/planning-type')]
final class PlanningTypeController extends AbstractController
{

    #[Route('/{id}', name: 'api_planning_type_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non autorisé'], 401);
        }
        $planning = $entityManager->getRepository(PlanningType::class)->find($id);

        if (!$planning) {
            return new JsonResponse(['error' => 'Planning introuvable'], 404);
        }
        // Optionnel : vérifier que l’utilisateur est bien l’auteur du planning
        if ($planning->getAuthor() !== $user) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }
        $data = [
            'id' => $planning->getId(),
            'name' => $planning->getName(),
            'weeks' => [],
        ];
        foreach ($planning->getWeeks() as $week) {
            $weekData = [
                'id' => $week->getId(),
                'name' => $week->getName(),
                'days' => [],
            ];
            foreach ($week->getDayTypes() as $day) {
                $dayData = [
                    'id' => $day->getId(),
                    'name' => $day->getName(),
                    'slots' => [],
                ];
                foreach ($day->getSlotTypes() as $slot) {
                    $dayData['slots'][] = [
                        'id' => $slot->getId(),
                        'startTime' => $slot->getStartTime()->format('H:i'),
                        'endTime' => $slot->getEndTime()->format('H:i'),
                        'color' => $slot->getColor(),
                    ];
                }
                $weekData['days'][] = $dayData;
            }
            $data['weeks'][] = $weekData;
        }
        return new JsonResponse($data);
    }


    #[Route('/create', name: 'api_planning_type_create', methods: ['POST'])]
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
        $group = $entityManager->getRepository(\App\Entity\Group::class)->find($groupId);
        if (!$group) {
            return new JsonResponse(['error' => 'Groupe introuvable'], 404);
        }
        // Vérifie si le groupe a déjà un planning
        if ($group->getPlanningType()) {
            return new JsonResponse(['error' => 'Ce groupe a déjà un planning'], 409);
        }
        $planning = new PlanningType();
        $planning->setName($name);
        $planning->setAuthor($user);
        $planning->setCreatedAt(new \DateTimeImmutable());
        // Lier le planning au groupe
        $group->setPlanningType($planning);

        $entityManager->persist($planning);
        $entityManager->flush();

        return $this->json([
            'id' => $planning->getId(),
            'name' => $planning->getName()
        ], 201);
    }

    #[Route('/save', name: 'api_planning_type_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Non autorisé'], 401);
        }

        if (empty($data['name']) || empty($data['weeks'])) {
            return new JsonResponse(['error' => 'Nom ou semaines manquantes'], 400);
        }

        $planning = new PlanningType();
        $planning->setName($data['name']);
        $planning->setAuthor($user);
        $planning->setCreatedAt(new \DateTimeImmutable());

        foreach ($data['weeks'] as $weekData) {
            $week = new WeekType();
            $week->setName($weekData['name']);
            $week->setPlanningType($planning);

            foreach ($weekData['days'] as $dayData) {
                $day = new DayType();
                $day->setName($dayData['name']);
                $day->setWeek($week);

                foreach ($dayData['slots'] as $slotData) {
                    $slot = new SlotType();
                    $slot->setStartTime(new \DateTime($slotData['startTime']));
                    $slot->setEndTime(new \DateTime($slotData['endTime']));
                    $slot->setColor($slotData['color']);
                    $slot->setDay($day);

                    $day->addSlotType($slot);
                }

                $week->addDayType($day);
            }

            $planning->addWeek($week);
        }

        $entityManager->persist($planning);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Planning enregistré avec succès',
            'planningId' => $planning->getId()
        ], Response::HTTP_CREATED);
    }
}

