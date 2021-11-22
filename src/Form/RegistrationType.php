<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username',
                TextType::class,
                array('invalid_message' => "The username is not valid"))
            ->add('email', EmailType::class)
            ->add('password',RepeatedType::class,
                [    'invalid_message' => "Ooops...It seems like the passwords do not match",
                    'type' => PasswordType::class,
                    'options' => ['attr' => ['class' => 'password-field']],
                    'first_options' => [
                        'label' => "Password"
                    ],
                    'second_options'=> [
                        'label' => "Repeat password"
                    ]
                ]
            )
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
