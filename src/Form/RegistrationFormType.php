<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Image;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', null ,
                [
                    'label' => 'Pseudo *'
                ])
            ->add('prenom', null ,
                [
                    'label' => 'Prénom *'
                ])
            ->add('nom', null ,
                [
                    'label' => 'Nom *'
                ])
            ->add('telephone', null ,
                [
                    'label' => 'Telephone'
                ])
            ->add('email', null ,
            [
                'label' => 'Email *'
            ])
            ->add('campus', EntityType::class,
            [
                'class' => Campus::class,
                'required' => false,
                'choice_label' => 'nom',
                'placeholder' => 'Aucun campus',
                'label' => 'Campus'
            ])
            ->add('password', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe ne correspond pas. Veuillez le réécrire.',
                'options' => ['attr' => ['autocomplete' => 'new-password']],
//                'required' => true,
                'first_options' => ['label' => 'Mot de passe *'],
                'second_options' => ['label' => 'Confirmer mot de passe *'],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez rentrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
