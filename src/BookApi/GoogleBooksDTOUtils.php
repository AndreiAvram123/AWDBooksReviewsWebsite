<?php

namespace App\BookApi;

use App\Entity\Book;
use App\Entity\BookCategory;

use App\Repository\BookRepository;
use App\Repository\GoogleBookApiRepository;
use App\RequestModels\CreateBookReviewModel;
use Doctrine\ORM\EntityManagerInterface;


class GoogleBooksDTOUtils
{
    public function __construct(
        private GoogleBookApiRepository $googleBooksApiRepository,
        private BookRepository          $bookRepository,
        private EntityManagerInterface  $entityManager

    ){}

    private function persistBookDTOExtraData(
        GoogleBookDTO $googleBookDTO
    ){
        /**
         * @var $categories string[]
         */

       $categories = $googleBookDTO->getVolumeInfo()->getCategories();
       if($categories !== null) {
           $mappedCategories = array_map(function ($categoryString) {
               $categoryObject = new BookCategory();
               $categoryObject->setName($categoryString);
               return $categoryObject;
           }, $categories);
           foreach ($mappedCategories as $mappedCategory) {
               $this->entityManager->persist($mappedCategory);
           }
           $this->entityManager->flush();
       }

    }
    public function getGoogleBookFromRequest(
        CreateBookReviewModel $createBookReviewModel
    ):?Book{

        $book = $this->bookRepository->findByGoogleID(
            $createBookReviewModel->getGoogleBookID()
        );
        if($book !== null){
            return $book;
        }

        $bookDTO = $this->googleBooksApiRepository->getVolumeById(
            $createBookReviewModel->getGoogleBookID()
        );

        if($bookDTO !== null){
           $this->persistBookDTOExtraData($bookDTO);
            return $this->convertDTOToEntity($bookDTO) ;

        }

        return null;
    }

    public function convertDTOToEntity(GoogleBookDTO $googleBookDTO):Book{
        $googleBook = new Book();
        $googleBook->setTitle($googleBookDTO->getVolumeInfo()->getTitle());
        $googleBook->setGoogleBookID($googleBookDTO->getId());
        return $googleBook;

    }
}