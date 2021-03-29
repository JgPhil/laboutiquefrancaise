<?php

namespace App\Classes;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $products = [];

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function add(int $id)
    {
        $cart = $this->session->get('cart');

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function getCart()
    {
        return $this->session->get('cart');
    }
}
