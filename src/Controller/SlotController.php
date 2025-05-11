<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Entity\Day;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/slot')]
final class SlotController extends AbstractController
{
    #[Route('/add', name: 'api_slot_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        if (!$user) return new JsonResponse(['error' => 'Non autorisé'], 401);
        if (empty($data['startTime']) || empty($data['endTime']) || empty($data['dayId']) || empty($data['color'])) {
            return new JsonResponse(['error' => 'Données incomplètes'], 400);
        }
        $day = $entityManager->getRepository(Day::class)->find($data['dayId']);
        if (!$day) return new JsonResponse(['error' => 'Jour introuvable'], 404);

        $slot = new Slot();
        $slot->setStartTime(new \DateTime($data['startTime']));
        $slot->setEndTime(new \DateTime($data['endTime']));
        $slot->setColor($data['color']);
        $slot->setDay($day);
        // Gestion de l'employé si fourni
        if (!empty($data['employeeId'])) {
            $entityManagerployee = $entityManager->getRepository(Employee::class)->find($data['employeeId']);
            if ($entityManagerployee) {
                $slot->setEmployee($entityManagerployee);
            }
        }
        $entityManager->persist($slot);
        $entityManager->flush();
        return new JsonResponse([
            'message' => 'Créneau ajouté avec succès.',
            'slot' => $serializer->normalize($slot, null, ['groups' => ['slot:read']])
        ], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'api_slot_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) return new JsonResponse(['error' => 'Non autorisé'], 401);
        $slot = $entityManager->getRepository(Slot::class)->find($id);
        if (!$slot) return new JsonResponse(['error' => 'Créneau introuvable'], 404);
        $data = json_decode($request->getContent(), true);
        if (!isset($data['startTime'], $data['endTime'], $data['color'])) {
            return new JsonResponse(['error' => 'Données incomplètes'], 400);
        }
        $slot->setStartTime(new \DateTime($data['startTime']));
        $slot->setEndTime(new \DateTime($data['endTime']));
        $slot->setColor($data['color']);
        // Mise à jour de l'employé si fourni
        if (array_key_exists('employeeId', $data)) {
            if ($data['employeeId'] === null) {
                $slot->setEmployee(null);
            } else {
                $entityManagerployee = $entityManager->getRepository(Employee::class)->find($data['employeeId']);
                if ($entityManagerployee) {
                    $slot->setEmployee($entityManagerployee);
                }
            }
        }

        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Créneau mis à jour avec succès',
            'slot' => $serializer->normalize($slot, null, ['groups' => ['slot:read']])
        ]);
    }

    #[Route('/delete/{id}', name: 'api_slot_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) return new JsonResponse(['error' => 'Non autorisé'], 401);

        $slot = $entityManager->getRepository(Slot::class)->find($id);
        if (!$slot) return new JsonResponse(['error' => 'Créneau introuvable'], 404);

        $entityManager->remove($slot);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Créneau supprimé avec succès.']);
    }
}
