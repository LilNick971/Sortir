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
                ]);


            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function(FormEvent $event){
                    $sortie = $event->getData();
                    $form = $event->getForm();

                    $ville = $sortie->getVille() ?: null;

                    $this->addElements($form, $ville);
                }
            );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $ville = $this->em->getRepository(Ville::class)->find($data['ville']);

                $this->addElements($form, $ville);
            }
        );


    }

    protected function addElements(FormInterface $form, Ville $ville = null) {
        $form->add('ville', EntityType::class,
            [
                'required' => true,
                'data' => $ville,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une ville'
            ]);

        $lieux = array();

        if($ville){
            $repoLieu = $this->em->getRepository(Lieu::class);

            $lieux = $repoLieu->createQueryBuilder("q")
                ->where("q.ville = :villeId")
                ->setParameter("villeId", $ville->getId())
                ->getQuery()
                ->getResult();
        }

        $form->add('lieu', EntityType::class,
        [
            'required' => true,
            'placeholder' => 'Choisissez une ville en premier',
            'class' => Lieu::class,
            'choices' => $lieux,
            'choice_label' => 'nom'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
