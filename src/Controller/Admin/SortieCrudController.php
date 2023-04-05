<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use App\Entity\Sortie;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SortieCrudController extends AbstractCrudController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ){}
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function configureActions(
        Actions $actions
    ): Actions
    {
        $archiverSortie = Action::new('archivage', 'Archiver')
            ->displayAsLink()
            ->linkToCrudAction('archivage');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $archiverSortie)
            ->disable(Action::DELETE);
    }

    public function archivage(AdminContext $context){
        $sortieActuelle = $context->getEntity()->getInstance();

        $query_archive = 'INSERT INTO archive_sortie SELECT * FROM sortie WHERE sortie.id = :idSortie;';
        $statement = $this->entityManager->getConnection()->prepare($query_archive);
        $statement->bindValue('idSortie', $sortieActuelle->getId());
        $statement->executeStatement();

        $query_archive = 'DELETE FROM sortie WHERE sortie.id = :idSortie;';
        $statement = $this->entityManager->getConnection()->prepare($query_archive);
        $statement->bindValue('idSortie', $sortieActuelle->getId());
        $statement->executeStatement();
        return $this->redirectToRoute('admin',
        [
            'crudAction' => 'index',
            'crudControllerFqcn' => 'App\Controller\Admin\SortieCrudController'
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        $fields =  [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('organisateur'),
            AssociationField::new('siteOrganisateur'),
            AssociationField::new('lieu'),
            AssociationField::new('etat'),
            TextField::new('nom'),
            DateTimeField::new('dateHeureDebut'),
            TimeField::new('duree'),
            DateTimeField::new('dateLimiteInscription'),
            IntegerField::new('nbInscriptionsMax'),
            TextareaField::new('infosSortie')
        ];

        return $fields;
    }

}
