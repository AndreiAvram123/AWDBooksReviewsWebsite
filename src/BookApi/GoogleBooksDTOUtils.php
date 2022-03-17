<?php

namespace App\BookApi;

use App\Entity\BookCategory;
use App\Entity\BookReview;
use App\Entity\GoogleBook;
use App\Repository\BookCategoryRepository;
use App\Repository\GoogleBooksLocalRepository;
use App\Repository\GoogleBooksApiRepository;
use App\RequestModels\CreateBookReviewModel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use JetBrains\PhpStorm\Pure;

class GoogleBooksDTOUtils
{
    public function __construct(
        private GoogleBooksLocalRepository $googleBookLocalRepository,
        private GoogleBooksApiRepository   $googleBooksApiRepository,
        private EntityManagerInterface $entityManager

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
    ):?GoogleBook{

        $book = $this->googleBookLocalRepository->findByGoogleID(
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

    private function convertDTOToEntity(GoogleBookDTO $googleBookDTO):GoogleBook{
        $googleBook = new GoogleBook();
        $googleBook->setTitle($googleBookDTO->getVolumeInfo()->getTitle());
        $googleBook->setGoogleBookID($googleBookDTO->getId());
        return $googleBook;

    }
}