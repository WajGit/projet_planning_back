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

#[Route('/api/company')]
final class CompanyController extends AbstractController
{

    #[Route('/create', name: 'api_company_create', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);
        // dump($data);
        // die;
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
            'companyId' => $company->getId()
        ], Response::HTTP_CREATED);
    }

}
