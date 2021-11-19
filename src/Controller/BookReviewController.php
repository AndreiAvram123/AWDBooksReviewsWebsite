<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Entity\User;
use App\Form\BookReviewType;
use App\Form\RatingType;
use App\Form\RemoveBookReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookReviewController extends BaseController
{

    #[Route("/", name: "home", methods: ["GET"])]
    public function index(): Response{
        $allReviews = $this->getManager()
            ->getRepository(BookReview::class)
            ->findAvailableToUsers();

        return $this->render('index.html.twig', [
            'allReviews' => $allReviews
        ]);
    }

    #[Route("/reviews/user-reviews",name: "get_user_reviews",methods: ["GET"])]
    public function getUserReviews():Response{
        /** @var User $user */
        $user = $this->getUser();
        $reviews  = $user->getBookReviews() ;
        return $this->render('book_review/user_book_reviews.html.twig',
            [
                'reviews' => $reviews
            ]);
    }
    #[Route('/bookReview/{id}', name : "get_book_review_by_id")]
    public function displayBookReviewById(BookReview $bookReview, Request $request): Response
    {
        $removeBookReviewForm = $this->createForm(RemoveBookReviewType::class);
        $removeBookReviewForm->handleRequest($request);

        $ratingForm = $this->createForm(RatingType::class);
        $ratingForm->handleRequest($request);

        if($removeBookReviewForm -> isSubmitted() && $removeBookReviewForm-> isValid()){
            /** @var SubmitButton $removeButton */
            $removeButton = $removeBookReviewForm->get('Remove_book_review');
            if($removeButton->isClicked()){
                $bookReview->setDeclined(true);
                $this->persistAndFlush($bookReview);
                return  $this->redirectToRoute('home');
            }
        }
        if($this->canAccessFormData($ratingForm)){
            $rating = $bookReview->getRating();
            if($this->isFormButtonClicked($ratingForm,"like_button")){
                $rating->addLike();
            }
            if($this->isFormButtonClicked($ratingForm,"dislike_button")){
                $rating->addDislike();
            }
            $this->persistAndFlush($rating);
            return $this->redirectToRoute('get_book_review_by_id',['id'=>$bookReview->getId()]);
        }
        return $this-> renderForm('book_review/book_review.twig', [
            'bookReview' => $bookReview,
            'removeBookReviewForm'=>$removeBookReviewForm,
            'ratingForm' => $ratingForm
        ]);
    }


    private function canAccessFormData(FormInterface $form) : bool{
        return $form-> isSubmitted() && $form->isValid();
    }

    private function isFormButtonClicked(FormInterface $form, string $buttonName): bool
    {
        /** @var SubmitButton $button */
        $button = $form->get($buttonName);
        return $button->isClicked();
    }


    #[Route('/reviews/create', name: 'create_book_review', methods: ["GET","POST"])]
    public function createBookReview(Request $request): Response
    {
        $bookReview = new BookReview();
        $form = $this->createBookReviewForm($bookReview);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /** @var User $user */
            $user = $this->getUser();
            $bookReview = $form->getData();
            $bookReview->setCreator($user);
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('home');

        }
        return $this->renderForm('book_review/create_review.twig',[
            'form' => $form
        ]);
    }
    private function createBookReviewForm(BookReview $bookReview): FormInterface
    {
       return $this->createForm(BookReviewType::class,$bookReview);
    }

    #[Route('/books/{id}', name: 'edit_book_review')]
    public function editBookReview(Request $request, BookReview $bookReview):Response{
       if($bookReview -> getCreator() !==$this->getUser()){
           $this->redirectToRoute('home');
       }
       $form = $this->createBookReviewForm($bookReview);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
           $bookReview = $form->getData();
           $bookReview->setCreator($this->getUser());
           $this->persistAndFlush($bookReview);
           return $this->redirectToRoute('home');
       }
        return $this->renderForm('book_review/book_review_edit.html.twig',[
            'form' => $form
        ]);
    }

}
