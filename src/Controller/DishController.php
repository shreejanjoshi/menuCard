<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Repository\DishRepository;
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
    //in repo findoneby get individuel data but want to store entire data store in array
    public function index(DishRepository $dr): Response
    {
        $dishes = $dr->findAll();

        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes
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
