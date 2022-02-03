<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DishController extends AbstractController
{
    #[Route('/dish', name: 'dish')]
    public function index(): Response
    {
        return $this->render('dish/index.html.twig', [
            'controller_name' => 'DishController',
        ]);
    }
}
