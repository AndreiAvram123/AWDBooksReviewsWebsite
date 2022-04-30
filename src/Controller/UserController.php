<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Entity\User;
use App\Form\BookReviewType;
use App\Form\SubscribeType;
use App\Form\UserProfileType;
use App\utils\aws\AwsImageUtils;
use PHPUnit\Util\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class UserController extends BaseController
{

    #[Route('/users/{id}', name: 'user_profile')]
    public function getUserById(
        User $user,
        AwsImageUtils $awsImageUtils,
    ): Response{
        /** @var User $currentSessionUser */
        $currentSessionUser = $this->getUser();
        $userProfileForm = null;
        if(!is_null($currentSessionUser) && $currentSessionUser->getId() === $user->getId()){
            $userProfileForm = $this->createForm(UserProfileType::class, $currentSessionUser);

            if($this->canAccessFormData($userProfileForm)){
                $currentSessionUser = $userProfileForm->getData();
                $imageFile = $userProfileForm->get(UserProfileType::$user_profile_image_field)->getData();
                if($imageFile){
                    $image  = $awsImageUtils->uploadImageToBucketeer($imageFile);
                    $currentSessionUser->setProfileImage($image);
                    $this->persistAndFlush($currentSessionUser);
                    return $this->redirectToRoute('user_profile',['id' => $user->getId()]);
                }

            }
        }
        $userReviews = $user->getBookReviews()->filter(function (BookReview $review){
            return $review->getPending() === false && $review->getDeclined() === false;
        });

        $subscribeForm = $this->createForm(SubscribeType::class);
        if($subscribeForm->isSubmitted() && $subscribeForm->isValid()){
               if($currentSessionUser == null) throwException(new Exception("The user is null"));
               $user->addSubscriber($currentSessionUser);
               $this->persistAndFlush($user);
             return $this->redirectToRoute('user_profile',['id' => $user->getId()]);
        }

        return $this->renderForm('user/user_profile.twig', [
            'user' => $user,
            'userReviews'=> $userReviews,
            'userProfileForm' => $userProfileForm,
            'subscribeForm' => $subscribeForm
        ]);
    }

}
