<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/reg', name: 'reg')]
    public function reg(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = new User();

        //form
        $form = $this->createForm(RegistrationType::class, $user);
        //to send data to database
        $form->handleRequest($request);

        //if submit
        if($form->isSubmitted()){
            //entity manager
            $em = $doctrine->getManager();

            $em->persist();
            //to change in database
            $em->flush();

            return $this->redirect($this->generateUrl('dish.edit'));
        }

        return $this->render('registration/index.html.twig', [
            'regform' => $form->createView()
        ]);
    }
}
