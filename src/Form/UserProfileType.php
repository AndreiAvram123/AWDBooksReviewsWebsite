<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserProfileType extends AbstractType
{

    static string $user_profile_image_field = "profile_image";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('username')
            ->add('nickname')
            ->add('description')
            ->add('profile_image',FileType::class,[
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
            ->add('Save', SubmitType::class,
                [
                    'attr'=>[
                        'class' => 'axil-button-primary button-rounded'
                    ]
                ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
