<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;

#[Route('/user', name: 'user')]//Préfixe
class UserController extends AbstractController
{

    #[Route('/modifier', name: '_modifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->find(User::class, $this->getUser()->getId());
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/modifier.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER_ACTIF')]
    #[Route('/afficher/{user}', name: '_afficher')]
    public function afficher(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        return $this->render('user/afficher.html.twig', compact("user"));
    }



    #[IsGranted('ROLE_USER_ACTIF')]
    #[Route('/inscrire/{sortie}', name: '_inscrire')]
    public function inscrire(
        Request $request,
        EntityManagerInterface $entityManager,
        Sortie $sortie): Response
    {

        $user = $entityManager->find(User::class, $this->getUser()->getId());
        if($sortie->getNbInscriptionsMax() !== null and $sortie->getNbInscrits() < $sortie->getNbInscriptionsMax()) {
            $user->addSorty($sortie);
            $sortie->addUser($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_liste');
    }

    #[IsGranted('ROLE_USER_ACTIF')]
    #[Route('/desister/{sortie}', name: '_desister')]
    public function desister(
        Request $request,
        EntityManagerInterface $entityManager,
        Sortie $sortie
    ): Response
    {
        $this->getUser()->removeSorty($sortie);
        $sortie->removeUser($this->getUser());
        $entityManager->flush();

        return $this->redirectToRoute('sortie_liste');
    }
}
