<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\BookReviewType;
use App\Form\UserProfileType;
use App\utils\aws\AwsImageUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{

    #[Route('/users/{id}', name: 'user_profile')]
    public function getUserById(
        User $user,
        AwsImageUtils $awsImageUtils
    ): Response{
        /** @var User $currentSessionUser */
        $currentSessionUser = $this->getUser();
        if(!is_null($currentSessionUser)){
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
            return $this->renderForm('user/user_profile.twig', [
                'user' => $user,
                'userProfileForm' => $userProfileForm
            ]);
        }
        return $this->render('user/user_profile.twig', [
            'user' => $user
        ]);
    }

}
