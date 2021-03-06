<?php

namespace App\Form;

use App\Entity\BookReview;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
          if(!is_null($user)) {
              //add the like and dislike buttons only if the user is logged in
              $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) use ($user) {
                  /** @var BookReview $bookReview * */
                  $bookReview = $formEvent->getData();
                  $form = $formEvent->getForm();

                  $currentUserIsCreator =  $bookReview->getCreator()->getId() === $user->getId();
                  $alreadyRated = $bookReview->hasUserRating($user);

                  if ($alreadyRated === false && $currentUserIsCreator === false) {
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
