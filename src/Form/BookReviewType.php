<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookReview;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('summary', TextType::class)
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label'=> 'title',
                'placeholder' => 'Choose a book'
            ])
           ->add('find_book',ButtonType::class,
           [
              'attr' =>[
                  'class'=> 'btn btn-link',
                  'onclick' =>'window.location="/books/create"'
              ],
              'label'=>"Could not find your book? Click to add it"
           ])
          ->add('Save', SubmitType::class,);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookReview::class,
        ]);
    }
}
