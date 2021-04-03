<?php

namespace App\Controller\Order;

use App\Classes\Cart;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Order\OrderResponseController;

class OrderSuccessController extends OrderResponseController
{
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {        
        $order = $this->getOrder($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if (!$order->getIsPaid()) {
            $cart->remove();
            //TODO modifier statut commande
            $order->setIsPaid(true);
            $this->entityManager->flush();
            // TODO Envoyer mail confirmation commande

        }



        // Afficher informations commande de l'utilisateur

        return $this->render('order/success.html.twig', [
            'order' => $order
        ]);
    }
}
