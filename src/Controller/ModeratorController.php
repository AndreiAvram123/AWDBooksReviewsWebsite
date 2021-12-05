<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Form\ModeratorApproveType;
use App\Form\PendingReviewType;
use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModeratorController extends BaseController
{
    #[Route('/moderator', name: 'moderator')]
    public function index(
        BookReviewRepository $bookReviewRepo,
        BookRepository $booksRepo
    ): Response
    {
        $totalPendingReviews = $bookReviewRepo->countPending();
        $totalPendingBooks = $booksRepo ->countPending();

        return $this->render('moderator/moderator_index.twig', [
            'totalPendingReviews' => $totalPendingReviews,
            'totalPendingBooks' => $totalPendingBooks
        ]);
    }



    #[Route('moderator/pending/books/{id}', name: "pending_book")]
    public function pendingBook(Request $request, Book $book):Response{
        $moderatorForm = $this->createForm(ModeratorApproveType::class);
        if($this->canAccessFormData($moderatorForm)){
            if($this->isFormButtonClicked(form: $moderatorForm, buttonName: ModeratorApproveType::$approveButtonName)){
                $book->setPending(false);
            }
            if($this->isFormButtonClicked(form: $moderatorForm, buttonName: ModeratorApproveType::$declineButtonName)){
                $book->setPending(false);
                $book->setDeclined(true);
            }
            $this->persistAndFlush($book);
            return $this->redirectToRoute('moderator');
        }
        return $this->renderForm('book/book_page.twig',[
            'book' => $book,
            "moderatorForm" => $moderatorForm
        ]);
    }


    #[Route('moderator/bookReview/{id}', name: 'pending_book_review')]
    public function pendingBookReview(BookReview $bookReview):Response{
        $moderatorForm = $this->createForm(ModeratorApproveType::class);
        if($this->canAccessFormData($moderatorForm)){
            if($this->isFormButtonClicked(form: $moderatorForm, buttonName: ModeratorApproveType::$approveButtonName)){
                $bookReview->setPending(false);
            }
            if($this->isFormButtonClicked(form: $moderatorForm, buttonName: ModeratorApproveType::$declineButtonName)){
                $bookReview->setPending(false);
                $bookReview->setDeclined(true);
            }
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('home');
        }
        return $this->renderForm('book_review/book_review_moderator.twig', [
                "bookReview" => $bookReview,
                "moderatorForm" => $moderatorForm
            ]
        );
    }


    #[Route('/moderator/reviews/pending', name: 'pending_book_reviews')]
    public function pendingBookReviews():Response{
        $pendingReviews = $this
            ->getDoctrine()
            ->getRepository(BookReview::class)
            ->findPending();

        return $this->render('moderator/moderator_pending_reviews.twig', [
                'pendingReviews' => $pendingReviews
            ]
        );
    }

    #[Route('/moderator/books/pending', name: 'pending_books')]
    public function pendingBooks(
        BookRepository $bookRepository
    ):Response{
        return $this->render(
            'moderator/moderator_pending_books.twig', [
            'pendingBooks' => $bookRepository->findPending()
        ]);
    }
}
