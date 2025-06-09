<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'register', methods: ['POST'])]

    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        // Récupération des données JSON envoyées par React
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['message' => 'Utilisateur non enregistré'], Response::HTTP_BAD_REQUEST);
        }
        $existingUser = $userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé.'], Response::HTTP_CONFLICT); // 409
        } else {
            $user = new User();
            $user->setLastname($data['lastname']);
            $user->setFirstname($data['firstname']);
            $user->setEmail($data['email']);
            $user->setPhone($data['phone']);
            $user->setPassword($userPasswordHasher->hashPassword($user, $data['pwd']));

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Utilisateur créé avec succès'], 201);
        }
    }

    #[Route('/check-email', name: 'check_email', methods: ['POST'])]
    public function checkEmail(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email'])) {
            return new JsonResponse(['error' => 'Email manquant'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        return new JsonResponse(['exists' => $user !== null]);
    }
}
