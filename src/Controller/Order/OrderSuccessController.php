<?php

namespace App\Controller\Order;

use App\Classes\Cart;
use App\Classes\Mail\AppMailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Order\OrderResponseController;

class OrderSuccessController extends OrderResponseController
{
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(AppMailerInterface $mailer, Cart $cart, $stripeSessionId): Response
    {
        $order = $this->getOrder($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if (!$order->getState() == 0) {
            $cart->remove();
            $order->setState(1);
            $this->entityManager->flush();
            
            
            $content = "Bonjour " . $order->getUser()->getFirstname() . "<br>Merci pour votre commande<br>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas deserunt sit cumque itaque delectus magni excepturi, impedit eos? Dicta odio at dolor quidem enim sit est corrupti dolore qui deserunt!";
            $mailer->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Votre commande La Boutique Française est bien validée", $content);
        }



        // Afficher informations commande de l'utilisateur

        return $this->render('order/success.html.twig', [
            'order' => $order
        ]);
    }
}
