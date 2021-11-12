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
        return $this->render('book/index.html.twig', [
            'allReviews' => $allReviews
        ]);
    }



    #[Route('/book/{id}', name : "get_book_by_id",methods: ["GET","POST"])]
    public function displayBookById(Request $request): Response
    {
        $bookReview = new BookReview();
        $form = $this->createForm(BookReviewType::class, $bookReview);
        if($form->isSubmitted() && $form-> isValid()){
            $bookReview = $form->handleRequest($request);
            $this->persistAndFlush($bookReview);
            return $this->redirect("/");
        }
        return $this-> renderForm('book/index.html.twig',[
            'form' => $form
            ]
        );
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
