<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer, SluggerInterface $slugger): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('sortie_liste');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            // On génère un token et on l'enregistre
            $user->setActivationToken(md5(uniqid()));


            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            // On crée le message
            $message = (new TemplatedEmail())
                // On attribue l'expéditeur
                ->from('admin971@yopmail.com')
                // On attribue le destinataire
                ->to($user->getEmail())
//                // On crée le texte avec la vue
//                ->bcc(
//                    $this->renderView(
//                        'emails/activation.html.twig', ['token' => $user->getActivationToken()]
//                    ),
//                    'text/html'
//                )
                ->htmlTemplate('emails/activation.html.twig')
                ->context([
                    'token' => $user->getActivationToken()
                ])
            ;
            $mailer->send($message);


            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



    #[Route(path: '/activation/{token}', name: 'app_activation')]
    public function activation($token, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $userRepository->findOneBy(['activationToken' => $token]);
//        dd($user);

        // Si aucun utilisateur n'est associé à ce token
        if(is_null($user)){
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $user->setActivationToken(null);
//        dd($user);
        $entityManager->persist($user);
        $entityManager->flush();

        // On génère un message
        $this->addFlash('success', 'Utilisateur activé avec succès');

        // On retourne à la page de connexion
        return $this->redirectToRoute('app_login');
    }
}
