<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mail', name: 'mail')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $sit = 'sit1';
        $text = 'Please bring more salt';

        $email = (new TemplateEmail($mailer))
            ->from('sit1@menucard.wip')
            ->to('waiter@menucard.wip')
            ->subject('Order')

            ->htmlTemplate('mailer/mail.html.twig')

            ->context([
                'sit' => $sit,
                'text' => $text
            ]);

        $mailer->send($email);

        return new Response('email has been send successfully');
    }
}
