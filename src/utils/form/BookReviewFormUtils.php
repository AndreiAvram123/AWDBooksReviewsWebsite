<?php

namespace App\utils\form;

use App\BookApi\GoogleBooksDTOUtils;
use App\Entity\Book;
use App\Entity\BookReview;
use App\Entity\Image;
use App\Entity\ReviewSection;
use App\Entity\User;
use App\Form\BookReviewType;
use App\Repository\BookRepository;
use App\Repository\GoogleBookApiRepository;
use App\utils\aws\AwsImageUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class BookReviewFormUtils
{

    public function __construct(  private Security $security,
                                  private EntityManagerInterface $manager,
                                  private AwsImageUtils $awsImageUtils,
                                  private BookRepository $bookRepository,
                                  private GoogleBookApiRepository $googleBookApiRepository,
                                  private GoogleBooksDTOUtils $googleBooksDTOUtils
){}

    private string $googleBookRegex = "/google_book_id_[a-zA-Z0-9]+/";
    private string $bookRegex = "/book_id_[a-zA-Z0-9]+/";

    private function createReview(
        FormInterface $form,
        Request $request
    ):BookReview{
        /** @var User $user*/
        $user = $this->security->getUser();
        /**
         * @var BookReview $review
         */
        $review = $form->getData();
        $review->setCreator($user);
        $review->setPending(!$this->security->getUser()->isModerator());

        $book = $this->getBookFromInputValue(
            $request->request->get('selected-book-form-field')
        );
        $review->setBook($book);

        $review->setFrontImage(
            $this->uploadImageFromForm($form)
        );
        $this->addSectionsFromRequest($request->request, $review);
        return $review;
    }

    private function addSectionsFromRequest(
        InputBag $inputBag,
        BookReview $bookReview
    ){
        $bookReview->getSections()->clear();
        $numberOfSections = $inputBag->get('book_review')['number_sections'];

        for($sectionNumber= 1; $sectionNumber <= $numberOfSections; $sectionNumber ++){
            $section = new ReviewSection();
            $sectionTitle = $inputBag->get('section_' . $sectionNumber ."_title");
            $sectionSummary = $inputBag->get('section_' . $sectionNumber ."_summary");
            $section->setHeading($sectionTitle);
            $section->setText($sectionSummary);
            $bookReview->addSection($section);
        }

    }

    private function uploadImageFromForm(
        FormInterface $form
    ):?Image{
        $imageFile = $form->get(BookReviewType::$review_image_name)->getData();
        if($imageFile){
            return  $this->awsImageUtils->uploadImageToBucketeer($imageFile);
        }
        return null;
    }

    private function getBookFromInputValue(
        string $inputValue
    ):?Book{
        if($this->isGoogleBook($inputValue)){
            $googleBookID = $this->extractGoogleBookID($inputValue);
            $googleBook = $this->bookRepository->findByGoogleID($googleBookID);

            if($googleBook !== null){
                return $googleBook;
            }
            $googleBookDto = $this->googleBookApiRepository->getVolumeById($googleBookID);
            
            if($googleBookDto === null){
                return null;
            }
            $googleBook = $this->googleBooksDTOUtils->convertDTOToEntity(
                $googleBookDto
            );
            $this->manager->persist($googleBook);
            return $googleBook;
        }
        if($this->isExclusiveBook($inputValue)){
            $bookID = $this->extractBookID($inputValue);
            return $this->bookRepository->find($bookID);
        }
        return null;
    }

    private function isGoogleBook(
        string $inputValue
    ):bool{
        return preg_match($this->googleBookRegex,$inputValue) == 1;
    }

    private function extractGoogleBookID(
        string $inputValue
    ):string{
        return str_replace("google_book_id_","", $inputValue);
    }

    private function extractBookID(
        string $inputValue
    ):string{
        return str_replace("book_id_","", $inputValue);
    }


    private function isExclusiveBook(
        string $inputValue
    ):bool{
        return preg_match($this->bookRegex, $inputValue) == 1;
    }



    public function handleBookReviewForm(
        FormInterface $form,
        Request $request
    ){
        $bookReview = $this->createReview(
            form : $form,
            request: $request
        );

        $this->manager-> persist($bookReview);
        $this->manager->flush($bookReview);
    }

}