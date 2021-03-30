<?php

namespace App\Classes;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $cart;
    private $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->cart = $this->session->get('cart');
        $this->productRepository = $productRepository;
    }

    public function add(int $id)
    {
        if (!empty($this->cart[$id])) {
            $this->cart[$id]++;
        } else {
            $this->cart[$id] = 1;
        }
        $this->session->set('cart', $this->cart);
    }

    public function decrease(int $id)
    {
        if (!$this->cart[$id]) {
            return;
        }
        if ($this->cart[$id] > 1) {
            $this->cart[$id]--;
        } else {
            unset($this->cart[$id]);
        }
        $this->session->set('cart', $this->cart);
    }

    public function removeProduct(int $id)
    {
        if (!$this->cart[$id]) {
            return;
        }
        unset($this->cart[$id]);
        $this->session->set('cart', $this->cart);
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function getCart()
    {
        return $this->session->get('cart');
    }

    public function hydratedCartWithProducts(?array $cart): array
    {
        $hydratedCartWithProducts = [];
        
        if ($this->getCart()) {
            foreach ($cart as $id => $quantity) {

                $productObject = $this->productRepository->find($id);

                if (!$productObject) {
                    $this->removeProduct($id);
                    continue;
                }

                $hydratedCartWithProducts[] = [
                    'product' => $productObject,
                    'quantity' => $quantity
                ];
            }
        }
        return $hydratedCartWithProducts;
    }
}
