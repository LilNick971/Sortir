<?php

namespace App\Controller;

use App\Entity\Filtre;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\FiltreFormType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SortieController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    #[Route('/sortie/liste', name: 'sortie_liste')]
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

        $listeSortie =$sortieRepository->listeSortieAffichage();

        if ($form->isSubmitted() && $form->isValid()) {

            $listeSortie = $sortieRepository->findSearch($filtre, $this->getUser());

        }
        return $this->render('sortie/liste.html.twig',
            compact('listeSortie', 'form'),
        );

    }

    #[Route('/sortie/annuler/{sortie}', name: 'sortie_annuler')]
    public function annulerSortie(
        Sortie $sortie,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $etat = $entityManager->getRepository(Etat::class)->find(6);
        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_liste');
    }

    #[Route('/sortie/publier/{sortie}', name: 'sortie_publier')]
    public function publierSortie(
        Sortie $sortie,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $etat = $entityManager->getRepository(Etat::class)->find(2);
        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_liste');
    }

    #[Route('/sortie/modifier/{sortie}', name: 'sortie_modif')]
    public function modifSortie(
        Sortie $sortie,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $sortie->setVille($sortie->getLieu()->getVille());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $entityManager->persist($sortie);
            $entityManager->flush();

            return  $this->redirectToRoute('sortie_liste');

        }

        return $this->render('sortie/modif.html.twig',
            compact('sortieForm'),
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

            $entityManager->persist($sortie);
            $entityManager->flush();

            return  $this->redirectToRoute('sortie_liste');

        }

        return $this->render('sortie/ajout.html.twig',
            compact('sortieForm'),
        );
    }

    #[Route('/sortie/getLieuxVille', name: 'sortie_liste_lieux')]
    public function listeLieuxVille(
        Request $request,
    ): Response
    {
        $lieuxRepo = $this->em->getRepository(Lieu::class);

        $lieux = $lieuxRepo->createQueryBuilder("q")
            ->where("q.ville = :villeId")
            ->setParameter('villeId', $request->query->get('villeId'))
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach ($lieux as $lieu){
            $responseArray [] = array(
                "id" => $lieu->getId(),
                "nom" => $lieu->getNom()
            );
        }

        return new JsonResponse(($responseArray));
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
