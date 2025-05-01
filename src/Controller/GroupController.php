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

#[Route('/api/group')]
final class GroupController extends AbstractController
{
    #[Route('/add', name: 'app_group_add', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CompanyRepository $companyRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        if (empty($data['group']['name'])) {
            return new JsonResponse(['error' => 'Le nom est requis'], Response::HTTP_BAD_REQUEST);
        }
        $companyName = $data['company']['name'];
        $company = $companyRepository->findOneBy(['name' => $companyName]);
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
    
        return new JsonResponse([
            'message' => 'Groupe créé avec succès',
            'groupId' => $group->getId()
        ], Response::HTTP_CREATED);
    }

//     #[Route('/api/group', name: 'api_group_list', methods: ['GET'])]
//     public function list(Request $request, EntityManagerInterface $entityManager): JsonResponse
//     {
//         $user = $this->getUser();
//         if (!$user) {
//             return new JsonResponse(['error' => 'Non autorisé'], 401);
//         }

//         $companyId = $request->query->get('company');
//         if (!$companyId) {
//             return new JsonResponse(['error' => 'Paramètre company manquant'], 400);
//         }

//         $groups = $entityManager->getRepository(Group::class)->findBy(['company' => $companyId]);

//         $data = [];
//         foreach ($groups as $group) {
//             $data[] = [
//                 'id' => $group->getId(),
//                 'name' => $group->getName(),
//             ];
//         }

//         return new JsonResponse($data);
// }


}
