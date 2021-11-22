<?php

namespace App\Form;

use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('like_button', SubmitType::class, [
                'attr' =>[
                   'class'=>"btn-floating deep-purple"
                ],
                'icon'=>"fa-thumbs-up",
                'label'=>" "
            ])
            ->add('dislike_button', SubmitType::class,[
                'icon'=>"fa-thumbs-down",
                'attr' =>[
                    'class'=>"btn-floating deep-purple"
                ],
                'label' => " "
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
