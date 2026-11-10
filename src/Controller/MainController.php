<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class MainController extends AbstractController
{
    private array $messages = [
        ['message' => 'Hello', 'created' => '2022/06/12'],
        ['message' => 'Hi', 'created' => '2022/04/12'],
        ['message' => 'Bye!', 'created' => '2021/05/12']
    ];

    #[Route('/{limit?3}', name: 'app_index', requirements: ['limit' => Requirement::DIGITS])]
    public function index(int $limit): Response
    {
        // return new Response(
        //     implode(array_slice($this->messages, 0, $limit)) . ' World!'
        // );
        return $this->render(
            'main/index.html.twig',
            [
                'messages' => $this->messages,
                'limit' => $limit
            ]
        );
    }

    #[Route('/messages/{id<\d+>}', name: 'app_show_one')]
    public function showOne(mixed $id): Response
    {
        // return new Response($this->messages[$id]);
        return $this->render(
            'main/show-one.html.twig',
            [
                'message' => $this->messages[$id]
            ]
        );
    }
}
