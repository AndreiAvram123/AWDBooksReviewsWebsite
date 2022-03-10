<?php

namespace App\Controller\api;



use App\Repository\BookReviewRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookApiController extends AbstractFOSRestController
{

    #[Get("/api/reviews")]
    public function getReviewsByPage(
        Request $request,
        BookReviewRepository $bookReviewRepository
    ):JsonResponse{
        $page = $request->query->get("page");
        if($page === null){
            $page = 1;
        }
       return $this->json($bookReviewRepository->findPubliclyAvailable($page));
    }
}