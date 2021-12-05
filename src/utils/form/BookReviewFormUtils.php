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
use Symfony\Component\HttpFoundation\Response;
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
        $numberOfSections = $requestBag->get('book_review')['number_sections'];
        for($sectionNumber= 0; $sectionNumber < $numberOfSections; $sectionNumber ++){
            $section = new ReviewSection();
            $section->setBookReview($bookReview);
            $sectionTitle = $requestBag->get('section_' . $sectionNumber ."_title");
            $sectionSummary = $requestBag->get('section_' . $sectionNumber ."_summary");
            var_dump($sectionTitle);
            $section->setHeading($sectionTitle);
            $section->setText($sectionSummary);
            $this-> manager->persist($section);
        }

    }
    public function handleBookReviewForm(
        FormInterface $form,
        Request $request
    ){

            $bookReview = $this->createReviewFromFormData($form);
            $bookReview->setPending(true);
            $imageFile = $form->get(BookReviewType::$review_image_name)->getData();
            if($imageFile){
                $image  = $this->awsImageUtils->uploadImageToBucketeer($imageFile);
                $bookReview->setFrontImage($image);
                $this->manager-> persist($bookReview);
            }
            $this->createSections($request, $bookReview);
            $this->manager->flush($bookReview);
        }

}