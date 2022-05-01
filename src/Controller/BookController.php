<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Form\BookReviewType;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\utils\aws\AwsImageUtils;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends BaseController
{

    static int $itemsPerPage = 10;

    #[Route("/books/create", name: "create_book_path")]
    public function createBook(
        AwsImageUtils $awsImageUtils
    ):Response{
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        if($this->canAccessFormData($form)){
            /** @var Book $book */
            $book = $form->getData();
            if($this->getUser()->isModerator()){
                $book->setPending(false);
            }
            $uploadedImage = $form->get('image')->getData();
            if($uploadedImage){
                $image = $awsImageUtils->uploadImageToBucketeer($uploadedImage);
                $book->setImage($image);
            }
            $this->persistAndFlush($book);
            return $this->redirectToRoute('home');
        }
        return $this->renderForm("book/create_book.html.twig",
        [
            'form'=> $form
        ]);
    }

     #[Route("/books/{id}", name: 'book_path', requirements:['id' => '\d+'])]
     public function getBookById(Book $book): Response
     {
         return $this->render('book/book_page.twig',[
               'book' => $book
             ]
         );
     }

    #[Route("/books/{page}", name: 'books_page',  requirements:['page' => '\d+'])]
    public function showAllBooks(
        BookRepository $bookRepository,
        int $page = 1
    ): Response
    {
          $numberOfPages =  intval($bookRepository->countPubliclyAvailable()/self::$itemsPerPage);
          $books = $bookRepository->findPubliclyAvailable($page);
          return $this->render('book/books_list.twig',[
               'books' =>$books,
               'numberOfPages' => $numberOfPages
          ]);
    }

}
