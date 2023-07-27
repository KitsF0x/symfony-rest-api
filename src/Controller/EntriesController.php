<?php

namespace App\Controller;

use App\Entity\Entry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class EntriesController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/entries', name: 'entries_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $entries = $this->entityManager->getRepository(Entry::class)->findAll();

        $data = [];

        foreach($entries as $entry)
        {
            $data[] = [
                'id' => $entry->getId(),
                'name' => $entry->getTitle(),
                'description' => $entry->getDescription()
            ];
        }
        return $this->json($data);
    }

    #[Route('/entries', name: 'entries_create', methods: ['POST'])]
    public function create(Request $request) : JsonResponse
    {
        $entry = new Entry();

        $requestBody = json_decode($request->getContent(), true);

        $entry->setTitle($requestBody['title']);
        $entry->setDescription($requestBody['description']);

        $this->entityManager->persist($entry);
        $this->entityManager->flush();

        $data[] = [
            'id' => $entry->getId(),
            'name' => $entry->getTitle(),
            'description' => $entry->getDescription()
        ];

        return $this->json($data);
    }

    #[Route('/entries/{id}', name: 'entries_show', methods: ['GET'])]
    public function show(Request $request, int $id) : JsonResponse
    {
        $entry = $this->entityManager->getRepository(Entry::class)->find($id);
        if(!$entry) {
            return $this->json('Cannot find entry with the given id', 404);
        }
        $data[] = [
            'id' => $entry->getId(),
            'name' => $entry->getTitle(),
            'description' => $entry->getDescription()
        ];
        return $this->json($data);
    }

    #[Route('/entries/{id}', name: 'entries_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, int $id) : JsonResponse
    {
        $entry = $this->entityManager->getRepository(Entry::class)->find($id);
        if(!$entry) {
            return $this->json('Cannot find entry with the given id', 404);
        }

        $requestBody = json_decode($request->getContent(), true);

        $entry->setTitle($requestBody['title']);
        $entry->setDescription($requestBody['description']);
        $this->entityManager->flush();

        $data[] = [
            'id' => $entry->getId(),
            'name' => $entry->getTitle(),
            'description' => $entry->getDescription()
        ];
        return $this->json($data);
    }

    #[Route('/entries/{id}', name: 'entries_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id) : JsonResponse
    {
        $entry = $this->entityManager->getRepository(Entry::class)->find($id);
        if(!$entry) {
            return $this->json('Cannot find entry with the given id', 404);
        }
        $this->entityManager->remove($entry);
        $this->entityManager->flush();

        return $this->json('Deleted entry with id ' . $id);
    }
}
