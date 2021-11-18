<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Form\PendingReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModeratorController extends BaseController
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
            ->findPending();
        return $this->render('moderator/moderator_pending_reviews.twig',
            [
                'pendingReviews' => $pendingReviews]
        );
    }

    #[Route('moderator/bookReview/{id}', name: 'pending_book_review')]
    public function pendingBookReview(Request $request , BookReview $bookReview):Response{
        $form = $this->createForm(PendingReviewType::class,$bookReview);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            /** @var SubmitButton $declineButton */
            $declineButton = $form->get('Decline');
            /** @var SubmitButton $approveButton */
            $approveButton = $form->get("Approve");

            if($declineButton->isClicked()){
                $bookReview->setDeclined(true);
                $bookReview->setPending(false);
            }
            if($approveButton ->isClicked()){
                $bookReview->setPending(false);
            }
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('home');

        }
        return $this->renderForm('moderator/pending_book_review.twig', [
                'form' => $form
            ]
        );
    }
}
