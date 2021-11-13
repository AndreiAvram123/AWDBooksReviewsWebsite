<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Entity\User;
use App\Form\BookReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookReviewController extends BaseController
{

    #[Route("/", name: "home", methods: ["GET"])]
    public function index(): Response{
        $repo = $this->getManager()
            ->getRepository(BookReview::class);
        $allReviews = $repo->findAll();
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
        return $this->renderForm('book_review/create_review.twig',[
            'form' => $form
        ]);
    }
}
