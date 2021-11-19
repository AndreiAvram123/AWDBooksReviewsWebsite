<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Form\BookReviewType;
use App\Form\BookType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends BaseController
{
    #[Route("/books/create", name: "create_book",methods: ["GET","POST"])]
    public function createBook(Request $request):Response{
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $book = $form->getData();
            $this->persistAndFlush($book);
            return $this->redirectToRoute('home');
        }
        return $this->renderForm("book/create_book.html.twig",
        [
            'form'=> $form
        ]);
    }


}
