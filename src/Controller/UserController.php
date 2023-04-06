<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Image;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LectureCSVType;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user', name: 'user')]//Préfixe
class UserController extends AbstractController
{

    #[Route('/modifier', name: '_modifier')]
    public function modifier(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = $entityManager->find(User::class, $this->getUser()->getId());
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($userPasswordHasher->isPasswordValid($user, $form->get('checkPassword')->getData())) {
//                 On récupère les images transmises
                $imageFile = $form->get('image')->getData();

                // On boucle sur les images
                if ($imageFile) {
                    $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                    $safeFilename = $slugger->slug($originalFileName);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // On crée l'image dans la base de données
                    $img = new Image();
                    $img->setValeur($newFilename);

                    $user->setAvatar($img);
                }

                $newPassword = $form->get('newPassword')->getData();
                if ($newPassword){
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $newPassword
                        )
                    );
                }

                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $this->redirectToRoute('user_afficher',
                [
                    'participant' => $user->getId()
                ]);
            } else {
                $this->addFlash('echec', "Le mot de passe est incorrect, modifications annulées");
                return $this->redirectToRoute('sortie_ajout');
            }
        }

        return $this->render('user/modifier.html.twig',
            compact('form')
        );
    }

//    #[IsGranted('ROLE_USER_ACTIF')]
    #[Route('/afficher/{participant}', name: '_afficher')]
    public function afficher(
        User $participant,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_USER_ACTIF', $user->getRoles(),true) || $user->getId() === $participant->getId()){
            return $this->render('user/afficher.html.twig', compact("participant"));
        }
        return $this->redirectToRoute('sortie_liste');
    }



    #[IsGranted('ROLE_USER_ACTIF')]
    #[Route('/inscrire/{sortie}', name: '_inscrire')]
    public function inscrire(
        Request $request,
        EntityManagerInterface $entityManager,
        Sortie $sortie): Response
    {

        $user = $entityManager->find(User::class, $this->getUser()->getId());
        $etatOuvert = $entityManager->getRepository(Etat::class)->find(2);
        if(($sortie->getNbInscriptionsMax() !== null and $sortie->getNbInscrits() < $sortie->getNbInscriptionsMax()) and $sortie->getEtat() === $etatOuvert) {
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/ajoutmultiple', name: '_ajoutmultiple')]
    public function ajoutMultiple(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $csvForm = $this->createForm(LectureCSVType::class);
        $csvForm->handleRequest($request);

        if ($csvForm->isSubmitted() && $csvForm->isValid()){
            $fichiercsv = $csvForm->get('liste')->getData();
            if($fichiercsv){
                if(($handle = fopen($fichiercsv->getPathname(), "r")) !== false){
                    while(($data = fgetcsv($handle)) !== false) {
                        $user = new User();
                        $user->setPseudo($data[0]);
                        $user->setNom($data[1]);
                        $user->setPrenom($data[2]);
                        $user->setEmail($data[3]);
                        $plainPassword = $user->getNom().$user->getPrenom();
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $plainPassword
                            )
                        );
                        $user->setRoles(["ROLE_USER_ACTIF"]);
                        $entityManager->persist($user);
                        $entityManager->flush();
                    }
                }
            }
            return $this->redirectToRoute('admin');
        }

        return $this->render('user/ajoutcsv.html.twig',
            compact('csvForm')
        );
    }


}
