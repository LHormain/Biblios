<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('dateOfBirth', DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'label' => 'Date de naissance',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('dateOfDeath', DateType::class, [
                'input' => 'datetime_immutable',     // attention pour les date. verifier que c'est au format date en bdd et pas datetime
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date de décès',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('nationality', TextType::class, [
                'required' => false,
                'label' => 'Nationalité',
            ])
            ->add('books', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'label' => 'Liste des publications',
            ])
            ->add('certification', CheckboxType::class, [    // exemple de champs qui n'existe pas dans l'entité
                'mapped' => false,
                'label' => "Je certifie l'exactitude des informations fournies ",
                'constraints' => [
                    new Assert\IsTrue(message: 'Vous devez cocher la case pour ajouter un auteur.'),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
