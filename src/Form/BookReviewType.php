<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookReview;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BookReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label'=> 'title',
                'placeholder' => 'Choose a book',
            ])
            ->add('find_book',ButtonType::class,
                [
                    'attr' =>[
                        'class'=> 'btn btn-link',
                        'onclick' =>'window.location="/books/create"'
                    ],
                    'label'=>"Could not find your book? Click to add it"
                ])
            ->add('number_sections',NumberType::class,
            [
                'html5'=>true,
                 'attr' => [
                     'placeholder'=> "Number of sections",
                     'min' => 1,
                     'max' => 10,
                 ]
            ])
            ->add('Save', SubmitType::class,
            [
                'attr'=>[
                    'class' => 'axil-button button-rounded'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
