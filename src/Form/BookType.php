<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookAuthor;
use App\Entity\BookCategory;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', \Symfony\Component\Form\Extension\Core\Type\TextType::class,[
                'required' => true
            ])
            ->add('authors', EntityType::class, [
                'class' => BookAuthor::class,
                'choice_label'=> 'name',
                'placeholder' => 'Click here to select an author',
                'label' => "The author of the book",
                 'multiple' => true,
                'required' => true
            ])
            ->add('find_author',ButtonType::class,
                [
                    'attr' =>[
                        'class'=> 'btn btn-link'
                    ],
                    'label'=>"Could not find your author? Click here to add it",
                ])
            ->add('categories', EntityType::class,[
                'class' => BookCategory::class,
                'choice_label'=> 'name',
                'placeholder' => 'Click here to select a category',
                'label' => "Category",
                 'multiple'=>true,
                'required' => true
            ])
            ->add('image',FileType::class,[
                'label' => "The front image of the review",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => "Please upload a valid image"
                    ])
                ]
            ])
           ->add('save', SubmitType::class,[
               'attr'=>[
                   'class' => 'axil-button-primary button-rounded'
               ]
           ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
