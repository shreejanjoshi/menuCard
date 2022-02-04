<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

//act as a prefix for all other root with in controller
#[Route('/dish', name: 'dish.')]
class DishController extends AbstractController
{
    // /dish/dish
    #[Route('/', name: 'edit')]
    //in repo find-one-by get individuel data but want to store entire data store in array
    public function index(DishRepository $dr): Response
    {
        $dishes = $dr->findAll();

        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(ManagerRegistry $doctrine, Request $request): Response
    {
        $dish = new Dish();

        //form
        $form = $this->createForm(DishType::class, $dish);
        //to send data to database
        $form->handleRequest($request);

        //if submit
        if($form->isSubmitted()){
            //entity manager
            $em = $doctrine->getManager();
            $em->persist($dish);
            //to change in database
            $em->flush();

            return $this->redirect($this->generateUrl('dish.edit'));
        }

        //response
        return $this->render('dish/create.html.twig', [
            'createForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, DishRepository $dr, ManagerRegistry $doctrine){
        //entity manager
        $em = $doctrine->getManager();
        $dish = $dr->find($id);
        $em->remove($dish);
        //to change in database
        $em->flush();

        //flash message
        $this->addFlash('success','Dish was removed successfully');

        return $this->redirect($this->generateUrl('dish.edit'));
    }
}
