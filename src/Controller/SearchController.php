<?php

namespace App\Controller;

use App\Entity\BookReview;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends BaseController
{

    #[Route('/search', name: 'search')]
    public function search(Request $request):Response{
        $query = $request->query->get('_search_query');
        $results = $this
            ->getDoctrine()
            ->getRepository(BookReview::class)
            ->findAllByTitle($query);
        return $this->render('search/search_results.twig',
        [
            'results' => $results
        ]);
    }
}