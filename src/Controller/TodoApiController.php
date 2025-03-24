<?php 

namespace App\Controller;

use App\Entity\ToDo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// TodoApiController will handle API requests for the todos
class TodoApiController extends AbstractController
{
    #[Route('/api/todos', methods: ['GET'])]
    public function getCollection(EntityManagerInterface $entityManager): Response
    {
        // Fetch all todos from the database
        $todos = $entityManager->getRepository(ToDo::class)->findAll();

        // Return the todos as JSON with a status code
        return $this->json($todos, Response::HTTP_OK);
    }
    // Create a new todo
    #[Route('/api/todos', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response    
    {
        // Get the JSON data from the request and decode it
        $data = json_decode($request->getContent(), true);

        // Create a new todo entity and set its properties
        $todo = new ToDo();
        $todo->setTitle($data['title'] ?? 'Untitled');
        $todo->setDescription($data['description'] ?? 'No description');
        $todo->setCompleted($data['completed'] ?? false);

        // Save the todo to the database
        $entityManager->persist($todo);
        $entityManager->flush();

        // Return the todo as JSON with a status code
        return $this->json($todo, Response::HTTP_CREATED);
    }
    // Retrieve and read a single todo
    #[Route('/api/todos/{id}', methods: ['GET'])]
    public function getItem(int $id, EntityManagerInterface $entityManager): Response
    {
        // Find the todo in the database by its ID
        $todo = $entityManager->getRepository(ToDo::class)->find($id);

        // Return an error if the todo is not found
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        // Return the corresponding todo as JSON
        return $this->json($todo);
    }
    // Update a todo
    #[Route('/api/todos/{id}', methods: ['PUT'])]
    public function updateItem(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Find the todo in the database by its ID
        $todo = $entityManager->getRepository(ToDo::class)->find($id);

        // Return an error if the todo is not found
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        // Get the JSON data from the request and decode it
        $data = json_decode($request->getContent(), true);

        // Update the todo entity with the new data
        $todo->setTitle($data['title'] ?? $todo->getTitle());
        $todo->setDescription($data['description'] ?? $todo->getDescription());
        $todo->setCompleted($data['completed'] ?? $todo->getCompleted());

        // Save the updated todo to the database
        $entityManager->flush();

        // Return the updated todo as JSON
        return $this->json($todo);
    }
    // Delete a todo
    #[Route('/api/todos/{id}', methods: ['DELETE'])]
    public function deleteItem(int $id, EntityManagerInterface $entityManager): Response
    {
        // Find the todo in the database by its ID
        $todo = $entityManager->getRepository(ToDo::class)->find($id);

        // Return an error if the todo is not found
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        // Delete the todo from the database
        $entityManager->remove($todo);
        $entityManager->flush();

        // Return a success message
        return $this->json(['message' => 'Todo deleted'], Response::HTTP_OK);
    }
}