<?php

namespace App\Controller;

use App\Classes\Search;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private $repo;


    public function __construct(ProductRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request): Response
    {

        $search = new Search;

        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->repo->findWithSearch($search);
        } else {
            $products = $this->repo->findAll();
        }

        return $this->render('product/index.html.twig', [
            'form' => $form->createView(),
            'products' => $products
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product")
     *
     * @return void
     */
    public function show($slug)
    {
        $product = $this->repo->findOneBySlug($slug);
        if (!$product) {
            return $this->redirectToRoute('products');
        }
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
