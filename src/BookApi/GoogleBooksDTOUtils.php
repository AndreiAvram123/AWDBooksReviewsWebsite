<?php

namespace App\BookApi;

use App\Entity\Book;
use App\Entity\BookAuthor;
use App\Entity\BookCategory;

use App\Entity\Image;
use App\Repository\BookRepository;
use App\Repository\GoogleBookApiRepository;
use App\RequestModels\CreateBookReviewModel;
use Doctrine\Common\Collections\ArrayCollection;
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
        $image = new Image();
        if($googleBookDTO->getVolumeInfo() ->getImageLinks() != null) {
            $image->setUrl($googleBookDTO->getVolumeInfo()->getImageLinks()->getThumbnail());
        }
        $googleBook->setImage($image);

        $googleCategories = $googleBookDTO->getVolumeInfo()->getCategories();
        if($googleCategories != null){
            $categories = $this->convertGoogleCategories($googleCategories);
            $googleBook->setCategories(new ArrayCollection($categories));
        }

        if($googleBookDTO->getVolumeInfo()->getAuthors() != null){
            $authors = array_map(function (string $authorName) {
                $bookAuthor = new BookAuthor();
                $bookAuthor->setName($authorName);
                return $bookAuthor;
            }, $googleBookDTO->getVolumeInfo()->getAuthors());

            $googleBook->setAuthors(new ArrayCollection($authors));
        }

        return $googleBook;

    }

    private function convertGoogleCategories(array $categoryNames):array{
        return array_map(function (string $categoryName){
            $bookCategory = new BookCategory();
            $bookCategory->setName($categoryName);
            return $bookCategory;
        },$categoryNames);
    }
}