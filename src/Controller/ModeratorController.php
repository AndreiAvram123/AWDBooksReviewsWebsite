<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookReview;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModeratorController extends AbstractController
{
    #[Route('/moderator', name: 'moderator')]
    public function index(): Response
    {
        $totalPendingReviews = $this
            ->getDoctrine()
            ->getRepository(BookReview::class)
            ->count(array('pending' => true));

        $totalPendingBooks = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->count(array('pending' => true));

        return $this->render('moderator/moderator_index.twig', [
           'totalPendingReviews' => $totalPendingReviews,
            'totalBooksPending' => $totalPendingBooks
        ]);
    }

    #[Route('moderator/pendingBookReviews', name: 'pending_book_reviews')]
    public function pendingBookReviews():Response{
        $pendingReviews = $this
            ->getDoctrine()
            ->getRepository(BookReview::class)
            ->findBy(array('pending' => true));
        return $this->render('moderator/moderator_pending_reviews.twig',
        [
            'pendingReviews' => $pendingReviews]
        );
    }
}
