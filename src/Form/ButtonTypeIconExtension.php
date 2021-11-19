<?php

namespace App\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonTypeIconExtension extends AbstractTypeExtension
{
    //here wer take the options passed and add it to the bulider
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('icon', $options['icon']);

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['icon'] = $options['icon'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['icon' => null]);
        $resolver->setDefined(['icon']);
    }

    public static function getExtendedTypes():iterable
    {
        yield ButtonType::class;
    }
}