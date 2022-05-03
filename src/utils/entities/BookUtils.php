<?php

namespace App\utils\entities;

use App\Entity\Book;
use App\Repository\BookAuthorRepository;
use App\Repository\BookCategoryRepository;
use App\RequestModels\CreateBookRequestModel;
use Doctrine\Common\Collections\ArrayCollection;

class BookUtils
{
    private function getCategoriesFromRequest(
        CreateBookRequestModel $createBookRequestModel,
        BookCategoryRepository $bookCategoryRepository
    ):ArrayCollection{
        $categories = [];
        foreach ($createBookRequestModel->getCategoriesIDs() as $categoriesID){
            $bookCategory = $bookCategoryRepository->find($categoriesID);
            $categories[] = $bookCategory;
        }
        return new ArrayCollection($categories);
    }
    private function getAuthorsFromRequest(
        CreateBookRequestModel $createBookRequestModel,
        BookAuthorRepository $bookAuthorRepository
    ):ArrayCollection{
        $authors = [];
        foreach ($createBookRequestModel->getAuthorsIDs() as $authorID){
            $author = $bookAuthorRepository->find($authorID);
            $authors[] = $author;
        }
        return new ArrayCollection($authors);
    }

    public function createBookFromRequest(
        CreateBookRequestModel $createBookRequestModel,
        BookCategoryRepository $bookCategoryRepository,
        BookAuthorRepository $bookAuthorRepository
    ):Book{
        $book = new Book();
        $book->setTitle($createBookRequestModel->getTitle());
        $book->setCategories($this->getCategoriesFromRequest(
            $createBookRequestModel,
            $bookCategoryRepository
        ));
        $book->setAuthors(
            $this->getAuthorsFromRequest(
                $createBookRequestModel,
                $bookAuthorRepository
            )
        );
        return $book;
    }

}