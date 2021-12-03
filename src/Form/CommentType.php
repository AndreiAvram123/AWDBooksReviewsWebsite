<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('summary', TextareaType::class,[
                'attr' => [
                    'maxlength' =>  100,
                      'placeholder' => "Enter your comment"
                ]
            ])
             ->add('post_comment_button', SubmitType::class,[
                 'label' => "Post comment",
                 'attr'=>[
                     'class' => 'axil-button-primary button-rounded'
                 ]
             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }


}
