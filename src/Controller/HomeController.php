<?php

namespace App\Controller;

use App\Entity\Header;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }


    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $this->entityManager->getRepository(Product::class)->findByIsBest(1),
            'headers' =>  $this->entityManager->getRepository(Header::class)->findAll()
        ]);
    }
}
