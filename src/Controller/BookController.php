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
    #[Route("/books/create", name: "create_book")]
    public function createBook(
        Request $request,
        AwsImageUtils $awsImageUtils
    ):Response{
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
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


}
