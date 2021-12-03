<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeratorApproveType extends AbstractType
{

    static string $approveButtonName = "Approve";
    static string $declineButtonName = "Decline";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(self::$approveButtonName, SubmitType::class,
                [
                    'attr' => [
                        'class' => 'axil-button-primary button-rounded'
                    ]
                ])
            ->add(self::$declineButtonName, SubmitType::class,
            [
                'attr' => [
                    'class' => 'axil-button-red button-rounded'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
