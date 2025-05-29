<?php

namespace App\Controller;

use App\Entity\Company;
// use App\Form\CompanyType;
// use App\Repository\CompanyRepository;
// use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/company')]
final class CompanyController extends AbstractController
{

  #[Route('', name: 'api_company_list', methods: ['GET'])]
  public function list(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
  {
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Non autorisé'], 401);
    }
    $companies = $entityManager->getRepository(Company::class)->findBy(['author' => $user]);
    $json = $serializer->serialize($companies, 'json', ['groups' => 'company:read']);
    return new JsonResponse($json, 200, [], true); // true = déjà en JSON
  }

  #[Route('/create', name: 'api_company_create', methods: ['POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    $data = json_decode($request->getContent(), true);
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Unauthorized'], 401);
    }
    if (empty($data['name'])) {
      return new JsonResponse(['error' => 'Le nom est requis'], Response::HTTP_BAD_REQUEST);
    }
    $company = new Company();
    $company->setName($data['name']);
    $company->setAuthor($user);
    $entityManager->persist($company);
    $entityManager->flush();
    return new JsonResponse([
      'message' => 'Entreprise créée avec succès',
      'id' => $company->getId(),
      'name' => $company->getName()
    ], Response::HTTP_CREATED);
  }

  #[Route('/edit', name: 'api_company_edit', methods: ['PUT'])]
  public function edit(Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Unauthorized'], 401);
    }
    if (empty($data['companyId']) || empty($data['name'])) {
      return new JsonResponse(['error' => 'ID et nom requis'], Response::HTTP_BAD_REQUEST);
    }
    $company = $entityManager->getRepository(Company::class)->find($data['companyId']);
    if (!$company || $company->getAuthor() !== $user) {
      return new JsonResponse(['error' => "Entreprise introuvable ou accès interdit"], 404);
    }
    $company->setName($data['name']);
    $entityManager->flush();
    return new JsonResponse([
      'message' => 'Entreprise modifiée avec succès',
      'id' => $company->getId(),
      'name' => $company->getName()
    ]);
  }

  #[Route('/delete/{id}', name: 'api_company_delete', methods: ['DELETE'])]
  public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    $user = $this->getUser();
    if (!$user) {
      return new JsonResponse(['error' => 'Unauthorized'], 401);
    }
    $company = $entityManager->getRepository(Company::class)->find($id);
    if (!$company || $company->getAuthor() !== $user) {
      return new JsonResponse(['error' => "Entreprise introuvable ou accès interdit"], 404);
    }
    $entityManager->remove($company);
    $entityManager->flush();
    return new JsonResponse(['message' => "Entreprise supprimée avec succès"]);
  }


}
