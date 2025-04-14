<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/employee')]
final class EmployeeController extends AbstractController
{
    #[Route(name: 'app_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->findAll();
        $data = [];

        foreach ($employees as $employee) {
            $data[] = [
                'id' => $employee->getId(),
                'firstname' => $employee->getFirstname(),
                'lastname' => $employee->getLastname(),
                'phone' => $employee->getPhone(),
                'email' => $employee->getEmail(),
                'photo' => $employee->getPhoto(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $employee = new Employee();
        $employee->setFirstname($data['firstname'] ?? '');
        $employee->setLastname($data['lastname'] ?? '');
        $employee->setPhone($data['phone'] ?? '');
        $employee->setEmail($data['email'] ?? '');
        $employee->setPhoto($data['photo'] ?? null);
        $employee->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($employee);
        $entityManager->flush();

        return $this->json($employee, 200, [], ['groups' => 'employee:read']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, EmployeeRepository $repository, EntityManagerInterface $entityManager): JsonResponse
    {
        $employee = $repository->find($id);

        if (!$employee) {
            return $this->json(['message' => 'Employee not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $employee->setFirstname($data['firstname'] ?? $employee->getFirstname());
        $employee->setLastname($data['lastname'] ?? $employee->getLastname());
        $employee->setPhone($data['phone'] ?? $employee->getPhone());
        $employee->setEmail($data['email'] ?? $employee->getEmail());
        $employee->setPhoto($data['photo'] ?? $employee->getPhoto());

        $entityManager->flush();

        return $this->json(['message' => 'Employee updated']);
    }

    #[Route('/{id}', name: 'app_employee_delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
    }
}
