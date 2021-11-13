<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Form\BookReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookReviewController extends BaseController
{

    #[Route("/", name: "home", methods: ["GET"])]
    public function index(): Response{
        $repo = $this->getManager()->getRepository(BookReview::class);
        $allReviews = $repo->findAll();
        return $this->render('index.html.twig', [
            'allReviews' => $allReviews
        ]);
    }



    #[Route('/reviews/create', name: 'create_book_review', methods: ["GET","POST"])]
    public function createBookReview(Request $request): Response
    {
        $bookReview = new BookReview();
        $form = $this->createForm(BookReviewType::class,$bookReview);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $bookReview = $form->getData();
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('home');

        }
        $form = $this->createForm(BookReviewType::class,$bookReview);
        return $this->renderForm('book_review/create_review.twig',[
            'form' => $form
        ]);
    }
}
