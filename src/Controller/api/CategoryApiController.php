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
        $serializedData = $this->serializer->serialize(
            data: $bookCategoryRepository->findAll(),
            format: 'json'
        );
        return $this->jsonResponse(
            $serializedData
        );
    }

}