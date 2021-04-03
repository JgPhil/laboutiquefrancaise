<?php

namespace App\Controller\Order;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Order\OrderResponseController;

class OrderCancelController extends OrderResponseController
{
    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {        
        $order = $this->getOrder($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        // Envoyer email pour lindeiquer erreur

        return $this->render('order/cancel.html.twig', [
            'order' => $order
        ]);
    }
}
