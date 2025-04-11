<?php
namespace App\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils, SerializerInterface $serializer, Security $security): JsonResponse
    {
        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Utilisation de Security pour récupérer l'utilisateur actuel
        // qui estauthentifié par le système de sécurité et JWT token
        $user = $security->getUser();
        // Si l'utilisateur n'est pas authentifié
        // Token invalide ou autre problème, erreur 401
        if (!$user) {
            return new JsonResponse([
                'message' => 'Utilisateur non reconnu.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }
        // Si l'utilisateur est authentifié, on renvoie ses données
        // Sérialiser l'utilisateur en JSON
        // Seul les attributs de l'entité marqué avec Groups peuvent être lu
        try {
            $userData = $serializer->serialize($user, 'json', ['groups' => ['user:read']]);
            return new JsonResponse($userData, JsonResponse::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur interne la de la création des donnés.',
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony gère automatiquement la déconnexion lorsque tu configures le firewall
        // Il n'est pas nécessaire de rajouter du code ici
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
