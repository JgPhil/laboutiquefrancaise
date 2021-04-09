<?php

namespace App\Controller;

use App\Classes\Mail\AppMailerInterface;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->entityManager = $manager;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(AppMailerInterface $mailer, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $searchEmail = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            if (!$searchEmail) {
                $password = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                $this->entityManager->persist($user);
                //$this->entityManager->flush();

                $content = "Bonjour ". $user->getFirstname()."<br>Bienvenue sur la première boutique Made in France<br>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas deserunt sit cumque itaque delectus magni excepturi, impedit eos? Dicta odio at dolor quidem enim sit est corrupti dolore qui deserunt!";
                $mailer->send($user->getEmail(), $user->getFirstname(), "Bienvenue sur La Boutique Française", $content);
                
                $notification = "Votre insription s'erst bien déroulée, merci de valider le lien présent dans l'email qui vous a été envoyé";
            } else {
                $notification = "L'email que vous avez renseigné existe déja";
            }


            
        }
        return $this->render('register/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
