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
    #[Route('/user/{id}', name: 'user_profile')]
    public function getUserById(User $user): Response
    {
        return $this->render('user/user_profile.twig', [
            'user' => $user
        ]);
    }

    #[Route('/myProfile', name: 'my_profile')]
    public function getCurrentProfile(Request $request, AwsImageUtils $awsImageUtils): Response
    {
        /** @var User $currentLoggedInUser */
        $currentLoggedInUser = $this->getUser();
        $userForm = $this->createForm(UserProfileType::class, $currentLoggedInUser);
        $userForm->handleRequest($request);
        if($this->canAccessFormData($userForm)){
            $currentLoggedInUser = $userForm->getData();
            $imageFile = $userForm->get(UserProfileType::$user_profile_image_field)->getData();
            if($imageFile){
                $image  = $awsImageUtils->uploadImageToBucketeer($imageFile);
                $currentLoggedInUser->setProfileImage($image);
            }
            $this->persistAndFlush($currentLoggedInUser);
            return $this->redirectToRoute('my_profile');
        }
        return $this->renderForm('user/my_profile.twig', [
            'user' => $currentLoggedInUser,
             'userForm' => $userForm
        ]);
    }
}
