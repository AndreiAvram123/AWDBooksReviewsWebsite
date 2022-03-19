<?php

namespace App\Controller;

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
        GoogleBookApiRepository $googleBooksRepository
    ):Response{
        $query = $request->query->get('_search_query');

        $bookReviews = $bookReviewRepository->findByTitle($query);
        $users = $userRepository->findByUsernameQuery($query);
        $books = $bookRepository ->searchByTitle($query);
        $googleBooks = $googleBooksRepository->searchByTitle($query)->getItems();

        return $this->render('search/search_results.twig',
        [
            'bookReviews' => $bookReviews,
            'books' => $books,
            'users' => $users,
            'googleBooks'=> $googleBooks
        ]);
    }
}