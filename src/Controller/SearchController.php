<?php

namespace App\Controller;

use App\BookApi\GoogleBookDTO;
use App\BookApi\GoogleBooksDTOUtils;
use App\Entity\Book;
use App\Entity\BookReview;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use App\Repository\GoogleBookApiRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends BaseController
{

    #[Route('/search', name: 'search')]
    public function search(
        Request $request,
        BookReviewRepository $bookReviewRepository,
        BookRepository $bookRepository,
        UserRepository $userRepository,
        GoogleBookApiRepository $googleBooksRepository,
        GoogleBooksDTOUtils $booksDTOUtils
    ):Response{
        $query = $request->query->get('_search_query');

        $bookReviews = $bookReviewRepository->findByTitle($query);
        $users = $userRepository->findByUsernameQuery($query);
        $exclusiveBooks = $bookRepository ->searchByTitle($query);
        $googleBookResults  =  $googleBooksRepository->searchByTitle($query);
        $googleBooks = array_map(function (GoogleBookDTO $googleBookDTO) use ($booksDTOUtils){
              return $booksDTOUtils->convertDTOToEntity($googleBookDTO);
        },$googleBookResults);

        return $this->render('search/search_results.twig',
        [
            'bookReviews' => $bookReviews,
            'books' => array_merge($exclusiveBooks,$googleBooks),
            'users' => $users,
        ]);
    }
}