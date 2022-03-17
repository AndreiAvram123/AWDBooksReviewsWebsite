<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
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
    private int $MAX_NUMBER_SECTIONS = 10;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('book', SearchType::class,[
                'label' => "Book to review",
                'attr'=> [
                    'placeholder' => "Type a book title",
                ],
            ])
            ->add('title',TextType::class,[
                'label'=>"Add a title for the review"
            ])->add('find_book',ButtonType::class,
                [
                    'attr' =>[
                        'class'=> 'btn btn-link',
                        'onclick' =>'window.location="/books/create"'
                    ],
                    'label'=>"Could not find your book? Click here to add it"
                ])
            ->add('Save', SubmitType::class,
                [
                    'attr'=>[
                        'class' => 'axil-button-primary button-rounded'
                    ]
                ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $formEvent) {
            $form = $formEvent->getForm();
            /** @var BookReview $bookReview */
            $bookReview = $form->getData();
            $currentNumberSections = 0;
            $imageRequired = true;
            if($bookReview !== null) {
               $currentNumberSections = sizeof($bookReview->getSections());
               $imageRequired = $bookReview->getFrontImage() === null ;
            }
             $form ->add('review_image',FileType::class,$this->creatFileTypeOptions($imageRequired));

            $form->add('number_sections', NumberType::class,
                [
                    'html5' => true,
                    'mapped'=>false,
                    'attr' => [
                        'placeholder' => "Number of sections",
                        'min' => 1,
                        'max' => $this->MAX_NUMBER_SECTIONS,
                        'value' => $currentNumberSections
                    ]
                ]);
        });


    }

    private function creatFileTypeOptions(bool $imageRequired):array{
      return  [
            'label' => "The front image of the review",
            'mapped' => false,
            'required' => $imageRequired,
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png'
                    ],
                    'mimeTypesMessage' => "Please upload a valid image"
                ])
            ]
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookReview::class
        ]);
    }
}
