<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;

class AccountOrderController extends AbstractController
{

    /**
     * @Route("/compte/mes-commandes", name="account_order")
     */
    public function index(OrderRepository $orderRepo): Response
    {
        $orders = $orderRepo->findSuccessOrders($this->getUser());
        return $this->render('account/order.html.twig', [
            'orders' => $orders
        ]);
    }
}
