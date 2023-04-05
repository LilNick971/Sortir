<?php

namespace App\Controller\Admin;

use App\Entity\ArchiveSortie;
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

class ArchiveSortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArchiveSortie::class;
    }

    public function configureActions(
        Actions $actions
    ): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE, Action::NEW, Action::EDIT);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        $fields =  [
            IdField::new('id'),
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
