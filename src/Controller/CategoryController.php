<?php

namespace App\Controller;

use App\Entity\BookCategory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends BaseController
{
    #[Route('/categories', name: 'categories')]
    public function index(): Response
    {
        $categories = $this
            ->getDoctrine()
             ->getManager()
             ->getRepository(BookCategory::class)
             ->findAll();

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
