<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
class BookReviewType extends AbstractType
{
    static string $review_image_name = "review_image";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                'label'=>"Add a title for the review"
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label'=> 'title',
                'placeholder' => 'Click here to select a book',
                'label' => "The book to review",
                'query_builder' => function(BookRepository $bookRepository){
                  return $bookRepository->findPubliclyAvailableAsQB();
                }
            ])
            ->add('find_book',ButtonType::class,
                [
                    'attr' =>[
                        'class'=> 'btn btn-link',
                        'onclick' =>'window.location="/books/create"'
                    ],
                    'label'=>"Could not find your book? Click here to add it"
                ])
             ->add('profile_image',FileType::class,[
                 'label' => "The front image of the review",
                 'mapped' => false,
                 'required' => true,
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
                    'class' => 'axil-button-primary button-rounded'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
