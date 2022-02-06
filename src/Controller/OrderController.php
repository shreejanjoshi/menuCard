<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    #[Route('/orders/{id}', name: 'orders')]
    public function order(ManagerRegistry $doctrine, Request $request, Dish $dish){
        $order = new Order();
        $order->setSit('sit1');
        $order->setName($dish->getName());
        $order->setOrdernumber($dish->getId());
        $order->setPrice($dish->getPrice());
        $order->setStatus("open");

        //entity manager
        $em = $doctrine->getManager();
        $em->persist($order);
        $em->flush();

        $this->addFlash('order', $order->getName(). 'is added to order.');
        return $this->redirect($this->generateUrl('menu'));
    }
}
