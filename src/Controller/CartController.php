<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'hydratedCartWithProducts' => $this->hydratedCartWithProducts($this->cart->getCart())
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    public function add($id): Response
    {
        $this->cart->add($id);
        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/cart/remove", name="remove_my_cart")
     */
    public function remove(): Response
    {
        $this->cart->remove();
        return $this->redirectToRoute('products');
    }

    private function hydratedCartWithProducts(array $cart): array{
        $hydratedCartWithProducts = [];
        foreach ($cart as $id => $quantity) {
            $hydratedCartWithProducts[] = [
                'product' => $this->getDoctrine()->getRepository(Product::class)->find($id),
                'quantity' => $quantity
            ];
        }
        return $hydratedCartWithProducts;
    }
}
