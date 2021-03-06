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
    public function order(ManagerRegistry $doctrine, Dish $dish){
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

    #[Route("/status/{id},{status}", name: "status")]
    public function status(ManagerRegistry $doctrine, $id, $status){
        $em= $doctrine->getManager();
        $order = $em->getRepository(Order::class)->find($id);

        //change the value
        $order->setStatus($status);
        $em->flush();

        return $this->redirect($this->generateUrl('order'));
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function delete($id, OrderRepository $or, ManagerRegistry $doctrine){
        //entity manager
        $em = $doctrine->getManager();
        $order = $or->find($id);
        $em->remove($order);
        //to change in database
        $em->flush();

        return $this->redirect($this->generateUrl('order'));
    }
}
