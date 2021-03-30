<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Form\AdressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAdressController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    /**
     * @Route("/compte/adresses", name="account_adress")
     */
    public function index(): Response
    {
        return $this->render('account/adress.html.twig', []);
    }

    /**
     * @Route("/compte/ajouter-une-adresse", name="account_adress_add")
     */
    public function add(Request $request): Response
    {
        $adress = new Adress;
        $form = $this->createForm(AdressType::class, $adress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adress = $form->getData();
            $adress->setUser($this->getUser());
            $this->entityManager->persist($adress);
            $this->entityManager->flush();
            return  $this->redirectToRoute('account_adress');
        }
        return $this->render('account/adress_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter une adresse'
        ]);
    }


    /**
     * @Route("/compte/modifier-adresse/{id}", name="account_adress_edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $adress = $this->entityManager->getRepository(Adress::class)->find($id);

        if (!$adress || $adress->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('account_adress');
        }

        $form = $this->createForm(AdressType::class, $adress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adress = $form->getData();
            $this->entityManager->flush();
            return  $this->redirectToRoute('account_adress');
        }
        $adressName = $adress->getName();
        return $this->render('account/adress_form.html.twig', [
            'form' => $form->createView(),
            'title' => "Modifier l'adresse \"$adressName\""
        ]);
    }

    /**
     * @Route("/compte/supprimer-adresse/{id}", name="account_adress_delete")
     */
    public function delete(int $id): Response
    {
        $adress = $this->entityManager->getRepository(Adress::class)->find($id);

        if ($adress && $adress->getUser() == $this->getUser()) {
            $this->entityManager->remove($adress);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('account_adress');
    }
}
