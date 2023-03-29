<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SortieController extends AbstractController
{
    #[Route('/sortie/liste', name: 'sortie_liste')]
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

    #[Route('/sortie/ajout', name: 'sortie_ajout')]
    public function ajoutSorties(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $etat = $entityManager->getRepository(Etat::class)->find(1);
            $sortie->setEtat($etat);
        }

        return $this->render('sortie/ajout.html.twig',
            compact('sortieForm'),
        );
    }

    #[Route('/sortie/detail/{sortie}', name: 'sortie_detail')]
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
