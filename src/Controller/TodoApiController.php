<?php 

namespace App\Controller;

use App\Entity\ToDo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// TodoApiController will handle API requests for the todos
class TodoApiController extends AbstractController
{
    #[Route('/api/todos')]
    public function getCollection(EntityManagerInterface $entityManager): Response
    {
        // Fetch all todos from the database
        $todos = $entityManager->getRepository(ToDo::class)->findAll();

        // Return the todos to the index template
        return $this->render('/main/index.html.twig', [
            'todos' => $todos,
        ]);
    }
}