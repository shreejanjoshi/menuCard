<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\Input;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/reg', name: 'reg')]
    public function reg(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        //form
        $regform = $this->createForm(RegistrationType::class, $user);
        //to send data to database
        $regform->handleRequest($request);

        //if submit
        if($regform->isSubmitted()){
            //password hasher
            $input = $regform->get('password')->getData();
            $user->setPassword(
                $passwordHasher->hashPassword($user, $input)
            );

            //entity manager
            $em = $doctrine->getManager();

            $em->persist($user);
            //to change in database
            $em->flush();

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('registration/index.html.twig', [
            'regform' => $regform->createView()
        ]);
    }
}
