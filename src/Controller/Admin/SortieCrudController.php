<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
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
