<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Form\BookReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book/{id}', name : "get_book_by_id",methods: ["GET","POST"])]
    public function index(Request $request): Response
    {
        $bookReview = new BookReview();
        $form = $this->createForm(BookReviewType::class, $bookReview);
        return $this-> renderForm('book/index.html.twig',[
            'form' => $form
            ]
        );
    }
}
