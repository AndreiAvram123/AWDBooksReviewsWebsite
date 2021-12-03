<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Form\BookReviewType;
use App\Form\BookType;
use App\utils\aws\AwsImageUtils;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends BaseController
{

    static int $itemsPerPage = 10;

    #[Route("/books/create", name: "create_book")]
    public function createBook(
        AwsImageUtils $awsImageUtils
    ):Response{
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        if($this->canAccessFormData($form)){
            /** @var Book $book */
            $book = $form->getData();
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


    #[Route("/books/{page}", name: 'books_page',  requirements:['page' => '\d+'])]
    public function showAllBooks( int $page = 1): Response
    {
          $repo = $this->getManager()->getRepository(Book::class);
          $numberOfPages =  intval($repo->countAvailable()/self::$itemsPerPage);
          $books = $repo->findPubliclyAvailable($page);
          return $this->render('book/books_list.twig',[
               'books' =>$books,
               'numberOfPages' => $numberOfPages
          ]);
    }

}
