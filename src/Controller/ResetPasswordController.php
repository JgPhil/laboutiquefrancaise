<?php

namespace App\Controller;

use App\Classes\Mail\AppMailerInterface;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(AppMailerInterface $mailer, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')) {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
            if (!$user) {
                // Enregistrer en base la demande de reset avec la date le user_id, et le token
                $this->addFlash('notice', 'Pas de compte associé à cet email');
                return $this->redirectToRoute('home');
            }
            $resetPassword = new ResetPassword();
            $resetPassword
                ->setUser($user)
                ->setCreatedAt(new DateTime())
                ->setToken(uniqid());
            $this->entityManager->persist($resetPassword);
            $this->entityManager->flush();
            // Envoi d'un email au user avec un lien permettant la mise à jour du password
            $url = $this->generateUrl('update_password', ['token' => $resetPassword->getToken()]);
            $content = 'Bonjour ' . $user->getFirstName() . '<br>Vous avez demandé à réinitialiser votre mot de passe sur LA Boutique Française<br><hr>';
            $content .= 'Merci de bien vouloir cliquer sur le lien suivant pour <a href="' . $url . '">mettre à jour votre mot de passe</a>';
    
            $mailer->send($user->getEmail(), $user->getFirstName(), 'Réinitialiser votre mot de passe sur La Boutique Française', $content);
            $this->addFlash('notice', 'Vous allez recevoir un email avec la procédure de reset du mot de passe');
        }
        return $this->render('security/reset_password.html.twig', []);
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function updatePassword(UserPasswordEncoderInterface $encoder, Request $request,  $token)
    {
        $resetPassword = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);
        if (!$resetPassword) {
            return  $this->redirectToRoute('reset_password');
        }
        $user = $resetPassword->getUser();
        // Vértifier si le createdAt est égal à now() - 3h
        $now = new DateTime();
        if ($now > $resetPassword->getCreatedAt()->modify('+ 3 hour')) {
            $this->addFlash('notice', 'Votre demande de reset mot de passe a expiré, merci de la renouveller');
            return $this->redirectToRoute('reset_password');
        }
        //rendre une vue avec demande mot de passe + confirmation
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Encodage du mot de passe et flush()
            $newPwd = $form->get('password')->getData();
            $hashPwd = $encoder->encodePassword($user, $newPwd);
            $user->setPassword($hashPwd);
            $this->entityManager->flush();
            //Redirection
            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/update_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
