<?php

namespace App\Controller\Order;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }


    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    {
        if (empty($this->getUser()->getAdresses()->getValues())) {
            return $this->redirectToRoute('account_adress_add');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->hydratedCartWithProducts()
        ]);
    }


    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $carrier = $form->get('carriers')->getData();
            $delivery = $form->get('adresses')->getData();
            $deliveryContent = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $deliveryContent .= '<br>' . $delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliveryContent .= '<br>' . $delivery->getCompany();
            }
            $deliveryContent .= '<br>' . $delivery->getAdress();
            $deliveryContent .= '<br>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $deliveryContent .= '<br>' . $delivery->getCountry();

            $order = new Order;
            $date = new DateTime();

            $reference = $date->format('dmY') . '-' . uniqid();

            $order
                ->setReference($reference)
                ->setUser($this->getUser())
                ->setCreatedAt($date)
                ->setCarrierName($carrier->getName())
                ->setCarrierPrice($carrier->getPrice())
                ->setDelivery($deliveryContent)
                ->setState(0);

            $this->entityManager->persist($order);

            foreach ($cart->hydratedCartWithProducts() as $key => $product) {
                $orderDetails = new OrderDetails;
                $orderDetails
                    ->setMyOrder($order)
                    ->setPrice($product['product']->getPrice())
                    ->setProduct($product['product']->getName())
                    ->setQuantity($product['quantity'])
                    ->setTotal($product['product']->getPrice() * $product['quantity']);

                $this->entityManager->persist($orderDetails);
            }
            
            $this->entityManager->flush();


            return $this->render('order/add.html.twig', [
                'cart' => $cart->hydratedCartWithProducts(),
                'order' => $order,
                'reference' => $order->getReference()
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}
