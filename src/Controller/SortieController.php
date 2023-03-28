<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/liste', name: 'sortie_liste')]
    public function listeSorties(
        SortieRepository $sortieRepository,
    ): Response
    {
        $listeSortie =$sortieRepository->findBy([
            'etat' => 2
        ]);
        return $this->render('sortie/liste.html.twig',
            compact('listeSortie'),
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
