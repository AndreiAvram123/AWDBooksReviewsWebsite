<?php

namespace App\Controller;

use App\Entity\BookCategory;
use App\Repository\BookCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends BaseController
{
    #[Route('/categories', name: 'categories')]
    public function index(
        BookCategoryRepository $bookCategoryRepository
    ): Response
    {
        $categories = array_filter($bookCategoryRepository->findAll(),function (BookCategory $category){
            $found = false;
            foreach ($category->getBooks() as $book){
                if($book->getPending() === false){
                    $found = true;
                }
            }
            return $found;
        });
        return $this->render('category/categories.twig',[
            'categories' => $categories
        ]);
    }
    #[Route('/category/{name}', name: 'category_path')]
    public function category(
        BookCategory $category
    ): Response{
        return $this->render('category/category_books.twig',[
            'category' => $category
        ]);
    }
}
