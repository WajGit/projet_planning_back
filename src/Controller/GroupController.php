<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\CompanyRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/group')]
final class GroupController extends AbstractController
{

  #[Route('/{id}', name: 'api_group_show', methods: ['GET'])]
  public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    }
    $group = $entityManager->getRepository(Group::class)->find($id);
    if (!$group) {
      return new JsonResponse(['error' => 'Groupe introuvable'], 404);
    }
    return new JsonResponse([
      'message' => 'Affichage Group succès',
      'groupId' => $group->getId(),
      'planningId' => $group->getPlanningType()->getId(),
      'calendarId' => $group->getCalendar()->getId(),
    ], Response::HTTP_OK);
  }



  #[Route('/add', name: 'app_group_add', methods: ['POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager, CompanyRepository $companyRepository, SerializerInterface $serializer): Response
  {
    $data = json_decode($request->getContent(), true);
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Unauthorized'], 401);
    }
    if (empty($data['companyId']) || empty($data['group'])) {
      return new JsonResponse(['error' => 'id et nom est requis'], Response::HTTP_BAD_REQUEST);
    }

    $company = $companyRepository->find($data['companyId']);
    if (!$company) {
      return new JsonResponse(['error' => 'Entreprise non trouvée'], Response::HTTP_NOT_FOUND);
    }
    $group = new Group();
    $group->setName($data['group']['name']);
    $group->setStart(new \DateTime($data['group']['start']));
    $group->setEnd(new \DateTime($data['group']['end']));
    $group->setCompany($company);
    $entityManager->persist($group);
    $entityManager->flush();
    $json = $serializer->serialize($group, 'json', ['groups' => 'group:read']);
    return new JsonResponse($json, 200, [], true);
  }


  #[Route('/edit', name: 'api_group_edit', methods: ['PUT'])]
  public function edit(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Unauthorized'], 401);
    }
    if (empty($data['groupId']) || empty($data['name'])) {
      return new JsonResponse(['error' => 'ID et nom requis'], Response::HTTP_BAD_REQUEST);
    }
    $group = $entityManager->getRepository(Group::class)->find($data['groupId']);
    if (!$group || $group->getCompany()->getAuthor() !== $user) {
      return new JsonResponse(['error' => 'Groupe introuvable ou accès interdit'], 404);
    }
    $group->setName($data['name']);
    $entityManager->flush();
    $json = $serializer->serialize($group, 'json', ['groups' => 'group:read']);
    return new JsonResponse($json, 200, [], true);
  }

  #[Route('/{id}', name: 'api_group_delete', methods: ['DELETE'])]
  public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Unauthorized'], 401);
    }
    $group = $entityManager->getRepository(Group::class)->find($id);
    if (!$group || $group->getCompany()->getAuthor() !== $user) {
      return new JsonResponse(['error' => 'Groupe introuvable ou accès interdit'], 404);
    }
    $entityManager->remove($group);
    $entityManager->flush();
    return new JsonResponse(['message' => 'Groupe supprimé avec succès']);
  }


}
