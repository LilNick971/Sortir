<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Filtre;
use App\Entity\Sortie;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('nom', TextType::class, [
                'required' => false
            ])
            ->add('dateDebut', DateType::class, [
//                'html5' => false,
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('dateLimite', DateType::class, [
//                'html5' => false,
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('sortieOrganisateur', CheckboxType::class, [
                'label' => 'Sorties auxquelles dont je suis l\'organisateur/trice',
                'required' => false
            ])
            ->add('sortieInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit(e)',
                'required' => false
            ])
            ->add('sortieNonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e)',
                'required' => false
            ])
            ->add('sortiePassee', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filtre::class,
        ]);
    }
}
