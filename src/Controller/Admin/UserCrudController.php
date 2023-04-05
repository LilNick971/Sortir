<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormBuilderInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        public UserPasswordHasherInterface $userPasswordHasher,
        public EntityManagerInterface $entityManager
    ){}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $ajoutMultiple = Action::new('ajoutMultiple', 'Ajouter plusieurs utilisateurs')
            ->displayAsButton()
            ->linkToCrudAction('ajoutMultiple')
            ->createAsGlobalAction();

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $ajoutMultiple);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('pseudo'),
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('telephone'),
            EmailField::new('email'),
            AssociationField::new('campus')->setRequired(false),
            ChoiceField::new('roles')->setChoices(['ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER_ACTIF' => 'ROLE_USER_ACTIF'])
                ->allowMultipleChoices()->setRequired(false),
        ];
        $password = TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Répéter'],
                'mapped' => false
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms();

        $fields[] = $password;

        return $fields;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword() {
        return function($event) {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $password = $form->get('password')->getData();
            if ($password === null) {
                return;
            }

            $hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
            $form->getData()->setPassword($hash);
        };
    }

    private function ajoutMultiple(){

    }

}
