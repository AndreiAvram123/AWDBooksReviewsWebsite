<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Form\BookReviewType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    #[Route("/", name: "home", methods: ["GET"])]
    public function index(): Response{
        $repo = $this->getManager()->getRepository(BookReview::class);
        $allReviews = $repo->findAll();
        return $this->render('index.html.twig', [
            'allReviews' => $allReviews
        ]);
    }



    #[Route('/bookReview/{id}', name : "get_book_by_id",methods: ["GET"])]
    public function displayBookReviewById(BookReview $bookReview): Response
    {
        return $this-> render('book_review/book_review.html.twig', [
            'bookReview' => $bookReview
        ]);
    }

    private function getManager(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }
    private function persistAndFlush($object){
        $this->getManager()->persist($object);
        $this->getManager()->flush();
    }
}
