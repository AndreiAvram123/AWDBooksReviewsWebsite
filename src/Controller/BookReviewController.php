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
use App\Repository\BookReviewRepository;
use App\utils\aws\AwsImageUtils;
use App\utils\entities\RatingUtils;
use App\utils\form\BookReviewFormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookReviewController extends BaseController
{
    static int $itemsPerPage = 10;

    #[Route("/", name: "home")]
    public function index(): Response{
        return $this->redirectToRoute('book_reviews_page');
    }

    #[Route("/reviews/page/{page}", name: "book_reviews_page", requirements:['page' => '\d+'])]
    public function bookReviewsPage(
        BookReviewRepository $booksReviewRepo,
        int $page = 1,
    ): Response{

        $allReviews = $booksReviewRepo->findPubliclyAvailable($page);
        $featuredReviews = $booksReviewRepo->findFeaturedReviews();
        $numberOfPages =  intval($booksReviewRepo->countPubliclyAvailable()/self::$itemsPerPage);

        return $this->render('index.twig', [
            'allReviews' => $allReviews,
            'numberOfPages' => $numberOfPages,
            'featuredReviews'=>$featuredReviews,
        ]);
    }


    #[Route('/reviews/{id}', name : "book_review", requirements:['id' => '\d+'])]
    public function getBookReviewById(
        BookReview $bookReview,
        RatingUtils $ratingUtils

    ): Response
    {
        $comment = new Comment();

        $ratingForm = $this->createForm(RatingType::class, $bookReview);
        $commentForm = $this->createForm(CommentType::class,$comment);

        /**
         * Handle a new rating
         */
        if($this->canAccessFormData($ratingForm)){
            $isRatingPositive = $this->isFormButtonClicked($ratingForm,"like_button");
            $ratingUtils->addRatingToBookReview(
                isPositive: $isRatingPositive,
                bookReview: $bookReview
            );
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('book_review',[
                    'id'=>$bookReview->getId()]
            );
        }

        /**
         * Handle a new comment
         */
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



    #[Route('/reviews/create', name: 'create_book_review')]
    public function createBookReview(
        Request $request,
        BookReviewFormUtils $bookReviewFormUtils
    ): Response
    {
       $form = $this->createForm(BookReviewType::class);

       if($this->canAccessFormData($form)){
           $bookReviewFormUtils->handleBookReviewForm($form,$request);
           return $this->redirectToRoute('home');
       }
        return $this->renderForm('book_review/create_review.twig',[
            'form' => $form
        ]);

    }


    #[Route("/reviews/{id}/edit", name: 'edit_book_review')]
    public function editBookReview(
        BookReview $bookReview,
        Request $request,
        BookReviewFormUtils $bookReviewFormUtils
    ): Response{
        /** @var User $user */
        $user = $this->getUser();
        if($user->getId() !== $bookReview->getCreator()->getId()){
            throw $this->createAccessDeniedException("Trying to edit another user's review!");
        }
        //only the creator can edit the form
        $form = $this->createForm(BookReviewType::class,data:  $bookReview);
        if($this->canAccessFormData($form)){
            $bookReviewFormUtils->handleBookReviewForm($form, $request);
            return $this->redirectToRoute('home');
        }
        return $this->renderForm('book_review/create_review.twig',[
            'form' => $form
        ]);
    }


}
