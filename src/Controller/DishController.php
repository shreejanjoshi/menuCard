<?php

namespace App\Controller;

use App\Entity\Dish;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Resquest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

//act as a prefix for all other root with in controller
#[Route('/dish', name: 'dish.')]
class DishController extends AbstractController
{
    // /dish/dish
    #[Route('/', name: 'edit')]
    public function index(): Response
    {
        return $this->render('dish/index.html.twig', [
            'controller_name' => 'DishController',
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(ManagerRegistry $doctrine): Response
    {
        $dish = new Dish();
        $dish->setName('Pizza');

        //entity manager
        $em = $doctrine->getManager();
        $em->persist($dish);
        $em->flush();

        //response
        return new Response("Dish has been created");
    }
}
