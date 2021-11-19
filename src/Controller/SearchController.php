<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends BaseController
{

    #[Route('/search', name: 'search')]
    public function search(Request $request):Response{
        $query = $request->query->get('_search_query');

        $bookReviews = $this ->getDoctrine()
            ->getRepository(BookReview::class)
            ->findAllByTitle($query);
        $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findByUsernameQuery($query);


        return $this->render('search/search_results.twig',
        [
            'bookReviews' => $bookReviews,
            'users' => $users
        ]);
    }
}