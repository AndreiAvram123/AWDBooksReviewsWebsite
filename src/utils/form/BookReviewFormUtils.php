<?php

namespace App\utils\form;

use App\Entity\BookReview;
use App\Entity\ReviewSection;
use App\Entity\User;
use App\Form\BookReviewType;
use App\utils\aws\AwsImageUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class BookReviewFormUtils
{

    public function __construct(  private Security $security,
                                  private EntityManagerInterface $manager,
                                  private AwsImageUtils $awsImageUtils){

    }

    private function createReviewFromFormData(FormInterface $form):BookReview{
        /** @var User $user*/
        $user = $this->security->getUser();
        $review = $form->getData();
        $review->setCreator($user);
        return $review;
    }

    private function createSections(Request $request, BookReview $bookReview){
        $requestBag = $request->request;
        //remove old sections
        $bookReview->getSections()->clear();

        $numberOfSections = $requestBag->get('book_review')['number_sections'];
        for($sectionNumber= 1; $sectionNumber <= $numberOfSections; $sectionNumber ++){
            $section = new ReviewSection();
            $sectionTitle = $requestBag->get('section_' . $sectionNumber ."_title");
            $sectionSummary = $requestBag->get('section_' . $sectionNumber ."_summary");
            $section->setHeading($sectionTitle);
            $section->setText($sectionSummary);
            $bookReview->addSection($section);
        }
    }
    public function handleBookReviewForm(
        FormInterface $form,
        Request $request
    ){
        $bookReview = $this->createReviewFromFormData($form);
        //always when a review is created or edited flag it as pending
        $bookReview->setPending(true);
        $imageFile = $form->get(BookReviewType::$review_image_name)->getData();
        if($imageFile){
            $image  = $this->awsImageUtils->uploadImageToBucketeer($imageFile);
            $bookReview->setFrontImage($image);
        }
        $this->createSections($request, $bookReview);
        $this->manager-> persist($bookReview);
        $this->manager->flush($bookReview);
    }

}