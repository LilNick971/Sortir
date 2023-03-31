<?php

namespace App\Controller;

use App\Entity\Filtre;
use App\Entity\Sortie;
use App\Form\FiltreFormType;
use App\Repository\SortieRepository;
//use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{

//    public function filtre(EntityManagerInterface $entityManager, Request $request): FormInterface
//    {
//        $search = new Filtre();
//        $form = $this->createForm(FiltreFormType::class, $search);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($search);
//            $entityManager->flush();
//
//        }
//        return $form;
//    }


    #[Route('/liste', name: 'sortie_liste')]
    public function listeSorties(
        EntityManagerInterface $entityManagerInterface,
        SortieRepository $sortieRepository,
        Request $request,
    ): Response
    {
        $filtre = new Filtre();
        $form = $this->createForm(FiltreFormType::class, $filtre);
        $form->handleRequest($request);
//        $search = $sortieRepository->findSearch($filtre, $this->getUser());

        $listeSortie =$sortieRepository->findBy([
            'etat' => 2
        ]);

        if ($form->isSubmitted() && $form->isValid()) {

            $listeSortie = $sortieRepository->findSearch($filtre, $this->getUser());

        }

        return $this->render('sortie/liste.html.twig',
            compact('listeSortie', 'form'),
        );

    }

    #[Route('/sortie/{sortie}', name: 'sortie_detail')]
    public function uneSortie(
        SortieRepository $sortieRepository,
        Sortie $sortie,
    ): Response
    {
        return $this->render('sortie/detail.html.twig',
            compact('sortie'),
        );
    }
}
