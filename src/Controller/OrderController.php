<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function index(OrderRepository $or): Response
    {
        //find only order that matches certin criteria
        $order = $or->findBy([
            'sit' => 'sit1'
        ]);

        return $this->render('order/index.html.twig', [
            'ordering' => $order
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

        $this->addFlash('order', $order->getName(). ' is added to order.');
        return $this->redirect($this->generateUrl('menu'));
    }
}
