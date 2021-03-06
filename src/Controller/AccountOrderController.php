<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;

class AccountOrderController extends AbstractController
{

    private $entityManager;
    private $ORDER_STATE = [
        0 => 'Non Payée',
        1 => 'En cours de préparation',
        2 =>  'En cours de livraison'
    ];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte/mes-commandes", name="account_order")
     */
    public function index(): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
        return $this->render('account/order.html.twig', [
            'orders' => $orders,
            'orderState' => $this->ORDER_STATE
        ]);
    }

    /**
     * @Route("/compte/mes-commandes/{reference}", name="account_order_show")
     */
    public function show($reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('account_order');
        }

        return $this->render('account/show.html.twig', [
            'order' => $order,
            'orderState' => $this->ORDER_STATE
        ]);
    }
}
