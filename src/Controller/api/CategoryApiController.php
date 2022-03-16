<?php

namespace App\Controller\api;

use App\Repository\BookCategoryRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryApiController extends BaseRestController
{
    #[Get("/api/categories")]
    public function getAllCategories(
        BookCategoryRepository $bookCategoryRepository
    ):JsonResponse{

        $categories = $bookCategoryRepository->findAll();
        return $this->jsonResponse(
            $categories
        );
    }

}