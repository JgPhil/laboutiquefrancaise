<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\Header;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $mail = new Mail();
                $content = "test";
                $mail->send('filtre67@gmail.com', 'toto', "Bienvenue sur La Boutique Française", $content);
                
        return $this->render('home/index.html.twig', [
            'products' => $this->entityManager->getRepository(Product::class)->findByIsBest(1),
            'headers' =>  $this->entityManager->getRepository(Header::class)->findAll()
        ]);
    }
}
