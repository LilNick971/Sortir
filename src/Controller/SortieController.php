<?php

namespace App\Controller;

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
}
