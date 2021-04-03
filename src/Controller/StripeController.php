<?php

namespace App\Controller;


use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, $reference)
    {
       $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        if (!$order) {
            return new JsonResponse(['error' => 'order']);
        }
        $details = $order->getOrderDetails()->getValues();

        $productsForStripe = [];
        $YOUR_DOMAIN = 'http://localhost:8000';
       
        foreach ($details as $productRow) {
            $product = $entityManager->getRepository(Product::class)->findOneByName($productRow->getProduct());
            $productsForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $productRow->getPrice(),
                    'product_data' => [
                        'name' => $productRow->getProduct(),
                        'images' => [$YOUR_DOMAIN . '/uploads/products/' . $product->getIllustration()],
                    ],
                ],
                'quantity' => $productRow->getQuantity()
            ];
        }

        $productsForStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1
        ];

        Stripe::setApiKey('sk_test_51Ib5NQJ9nKfKm9DFDyVJGM8zXRdTdSBwJzW0qWPHPRGUuaRN1Jtq3m2sne1aMinB9PJjfax9wiB3SXgagbXZ9Biu004ZIJz3Oo');


        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [$productsForStripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();
        //dd($order);

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}
