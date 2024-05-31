<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Enum\BookCategories;
use App\Enum\BookStatus;
use App\Enum\MediaTypes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('isbn', TextType::class, [
                'label' => 'Numéro ISBN',
            ])
            ->add('cover', UrlType::class, [
                'label' => 'Couverture (lien URL)',
            ])
            ->add('editedAt', DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'label' => 'Date d édition',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('plot', TextareaType::class, [
                'label' => 'Synopsis',
            ])
            ->add('pageNumber', NumberType::class, [
                'label' => 'Nombre de page',
            ])
            ->add('status', EnumType::class, [
                'class' => BookStatus::class,
                'label' => 'Status',
            ])
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'choice_label' => 'name',
                'label' => 'Éditeur',
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
                'multiple' => true,
                'by_reference' => false,
                'label' => 'Auteurs',
            ])
            ->add('langue', TextType::class, [
                'required' => false,
                'label' => 'Langue',
            ])
            ->add('category', EnumType::class, [
                'class' => BookCategories::class,
                'label' => 'Catégorie',
            ])
            ->add('mediaType', EnumType::class, [
                'class' => MediaTypes::class,
                'label' => 'Type de média',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}