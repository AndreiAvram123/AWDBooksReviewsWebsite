<?php

namespace App\Form;

use App\Entity\BookReview;
use App\Entity\Rating;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use function PHPUnit\Framework\isNull;

class RatingType extends AbstractType
{
    public function __construct(private Security $security){

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
          /** @var User $user */
          $user = $this->security->getUser();
          if(!isNull($user)) {
              $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) use ($user) {
                  /** @var BookReview $bookReview * */
                  $bookReview = $formEvent->getData();
                  $form = $formEvent->getForm();

                  if ($bookReview->hasUserRating($user) === false) {
                      $form->add('like_button', SubmitType::class, [
                          'attr' => [
                              'class' => "btn-floating deep-purple"
                          ],
                          'icon' => "fa-thumbs-up",
                          'label' => " "
                      ])
                          ->add('dislike_button', SubmitType::class, [
                              'icon' => "fa-thumbs-down",
                              'attr' => [
                                  'class' => "btn-floating deep-purple"
                              ],
                              'label' => " "
                          ]);
                  }
              });
          }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
