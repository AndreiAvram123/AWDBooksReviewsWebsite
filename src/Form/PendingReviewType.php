<?php

namespace App\Form;

use App\Entity\BookReview;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PendingReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('summary',TextType::class, array('attr' => array('readonly' => true)))
            ->add('Approve', SubmitType::class)
            ->add('Decline', SubmitType::class,
            [
                'attr' => array('class' => 'btn btn-danger')
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookReview::class,
        ]);
    }
}
