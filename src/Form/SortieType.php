<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('siteOrganisateur', EntityType::class,
                [
                    'class' => Campus::class,
                    'choice_label' => 'nom'
                ])
            ->add('ville', EntityType::class,
            [
                'class' => Ville::class,
                'choice_label' => 'nom'
            ]);

            $formModifier = function (FormInterface $form, Ville $ville = null){
                $listeLieux = null === $ville ? [] : $ville->getLieus();

                $form->add('lieu',EntityType::class,
                    [
                        'class' => Lieu::class,
                        'choice_label' => 'nom',
                        'placeholder' => 'Choix de la ville ?',
                        'choices' => $listeLieux,
                    ]);
            };

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier) {
                    $data = $event->getData();

                    $formModifier($event->getForm(), $data->getVille());

                }
            );

            $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($formModifier) {
                    $ville = $event->getForm()->getData();

                    $formModifier($event->getForm()->getParent(), $ville);

                }
            );
//            ->add('lieu', EntityType::class,
//            [
//                'class' => Lieu::class,
//                'choice_label' => 'nom'
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
