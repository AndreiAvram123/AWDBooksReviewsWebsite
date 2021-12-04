<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Entity\Comment;
use App\Entity\NegativeRating;
use App\Entity\PositiveRating;
use App\Entity\UserRating;
use App\Entity\ReviewSection;
use App\Entity\User;
use App\Form\BookReviewType;
use App\Form\CommentType;
use App\Form\RatingType;
use App\utils\aws\AwsImageUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookReviewController extends BaseController
{
    static int $itemsPerPage = 10;

    #[Route("/", name: "home")]
    public function index(): Response{
        $repo = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(BookReview::class);
        $allReviews =  $repo->findPubliclyAvailable();
        $featuredReviews = $repo->findFeaturedReviews();

        $numberOfPages =  intval($repo->countPubliclyAvailable()/self::$itemsPerPage);

        return $this->render('index.twig', [
            'allReviews' => $allReviews,
            'featuredReviews'=>$featuredReviews,
            'numberOfPages' => $numberOfPages
        ]);
    }

    #[Route("/reviews/page/{page}", name: "book_reviews_page", requirements:['page' => '\d+'])]
    public function bookReviewsPage(int $page): Response{
        $repo = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(BookReview::class);
        $allReviews = $repo->findPubliclyAvailable($page);

        $numberOfPages =  intval($repo->count(array())/self::$itemsPerPage);
        return $this->render('index.twig', [
            'allReviews' => $allReviews,
            'numberOfPages' => $numberOfPages
        ]);
    }


    #[Route('/bookReviews/{id}', name : "book_review")]
    public function displayBookReviewById(BookReview $bookReview): Response
    {
        $comment = new Comment();

        $ratingForm = $this->createForm(RatingType::class, $bookReview);
        $commentForm = $this->createForm(CommentType::class,$comment);

        if($this->canAccessFormData($ratingForm)){
            $isRatingPositive = $this->isFormButtonClicked($ratingForm,"like_button");
            $this->addRatingToBookReview(
                isPositive: $isRatingPositive,
                bookReview: $bookReview
            );
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('book_review',[
                    'id'=>$bookReview->getId()]
            );
        }

        if($this->canAccessFormData($commentForm)){
            /** @var Comment $comment */
            $comment = $commentForm->getData();
            /** @var User $creator */
            $creator = $this->getUser();
            $comment -> setCreator($creator);
            $comment->setBookReview($bookReview);
            $this->persistAndFlush($comment);
            return $this->redirectToRoute('book_review',[
                    'id'=>$bookReview->getId()
                ]
            );
        }


        return $this-> renderForm('book_review/book_review.twig', [
            'bookReview' => $bookReview,
            'ratingForm' => $ratingForm,
            'commentForm'=>$commentForm
        ]);
    }

    private function addRatingToBookReview(bool $isPositive, BookReview $bookReview){
        $manager = $this->getDoctrine()->getManager();
        if($isPositive === true){
            $rating = $this->createPositiveRating();
            $manager->persist($rating);
            $bookReview->addPositiveRating($rating);
        }else{
            $rating = $this->createNegativeRating();
            $manager->persist($rating);
            $bookReview->addNegativeRating($rating);
        }
    }

    private function createPositiveRating():PositiveRating{
        $rating = new PositiveRating();
        $rating->setCreator($this->getUser());
        return  $rating;
    }
    private function createNegativeRating():NegativeRating{
        $rating = new NegativeRating();
        $rating->setCreator($this->getUser());
        return  $rating;
    }

    #[Route('/reviews/create', name: 'create_book_review')]
    public function createBookReview(Request $request, AwsImageUtils $awsImageUtils): Response
    {
        $form = $this->createForm(BookReviewType::class);

        if($form->isSubmitted() && $form->isValid()){
            $bookReview = $this->createReviewFromFormData($form);
            $imageFile = $form->get(BookReviewType::$review_image_name)->getData();
            if($imageFile){
                $image  = $awsImageUtils->uploadImageToBucketeer($imageFile);
                $bookReview->setFrontImage($image);
                $this->persistAndFlush($bookReview);
            }
            $this->createSections($request, $bookReview);
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('home');
        }
        return $this->renderForm('book_review/create_review.twig',[
            'form' => $form
        ]);
    }



    private function createReviewFromFormData(FormInterface $form):BookReview{
        /** @var User $user*/
        $user = $this->getUser();
        $review = new BookReview();
        $review->setCreator($user);
        $review->setBook($form->getData()['book']);
        $review->setTitle($form->getData()['title']);
        return $review;
    }

    private function createSections(Request $request, BookReview $bookReview){

        $requestBag = $request->request;
        $numberOfSections = $requestBag->get('book_review')['number_sections'];
        for($sectionNumber= 1; $sectionNumber <= $numberOfSections; $sectionNumber ++){
            $section = new ReviewSection();
            $section->setBookReview($bookReview);
            $sectionTitle = $requestBag->get('section_' . $sectionNumber ."_title");
            $sectionSummary = $requestBag->get('section_' . $sectionNumber ."_summary");
            $section->setHeading($sectionTitle);
            $section->setText($sectionSummary);
            $this->getDoctrine()->getManager()->persist($section);
        }

    }



}
