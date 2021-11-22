<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Rating;
use App\Entity\ReviewSection;
use App\Entity\User;
use App\Form\BookReviewType;
use App\Form\CommentType;
use App\Form\RatingType;
use App\Repository\BookReviewRepository;
use App\utils\aws\AwsImageUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookReviewController extends BaseController
{
    static int $itemsPerPage = 10;

    #[Route("/", name: "home")]
    public function index(): Response{
        $repo = $this->getManager()
            ->getRepository(BookReview::class);
        $allReviews =  $repo->findPubliclyAvailable();
        $numberOfPages =  intval($repo->countPubliclyAvailable()/self::$itemsPerPage);
        return $this->render('reviews_list.twig', [
            'allReviews' => $allReviews,
            'numberOfPages' => $numberOfPages
        ]);
    }

    #[Route("/reviews/page/{page}", name: "book_reviews_page", requirements:['page' => '\d+'])]
    public function bookReviewsPage(int $page): Response{
        $repo = $this->getManager()
            ->getRepository(BookReview::class);
        $allReviews = $repo->findPubliclyAvailable($page);

        $numberOfPages =  intval($repo->count(array())/self::$itemsPerPage);
        return $this->render('reviews_list.twig', [
            'allReviews' => $allReviews,
             'numberOfPages' => $numberOfPages
        ]);
    }

    #[Route("/reviews/user-reviews",name: "get_user_reviews",methods: ["GET"])]
    public function getUserReviews():Response{
        /** @var User $user */
        $user = $this->getUser();
        $reviews  = $user->getBookReviews() ;
        return $this->render('book_review/user_book_reviews.html.twig',
            [
                'reviews' => $reviews
            ]);
    }
    #[Route('/bookReview/{id}', name : "get_book_review_by_id")]
    public function displayBookReviewById(BookReview $bookReview,
                                          Request $request): Response
    {

        $ratingForm = $this->createForm(RatingType::class);
        $ratingForm->handleRequest($request);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class,$comment);
        $commentForm->handleRequest($request);

        if($this->canAccessFormData($ratingForm)){
            $rating = $bookReview->getRating();
            if($this->isFormButtonClicked($ratingForm,"like_button")){
                $rating->addLike();
            }
            if($this->isFormButtonClicked($ratingForm,"dislike_button")){
                $rating->addDislike();
            }
            $this->persistAndFlush($rating);
            return $this->redirectToRoute('get_book_review_by_id',['id'=>$bookReview->getId()]);
        }

        if($this->canAccessFormData($commentForm)){
            /** @var Comment $comment */
            $comment = $commentForm->getData();
            /** @var User $creator */
            $creator = $this->getUser();
            $comment -> setCreator($creator);
            $comment->setCreationDate(new \DateTime());
            $comment->setBookReview($bookReview);
            $this->persistAndFlush($comment);
            return $this->redirectToRoute('get_book_review_by_id',['id'=>$bookReview->getId()]);
        }



        return $this-> renderForm('book_review/book_review.twig', [
            'bookReview' => $bookReview,
            'ratingForm' => $ratingForm,
            'commentForm'=>$commentForm
        ]);
    }


    #[Route('/reviews/create', name: 'create_book_review')]
    public function createBookReview(Request $request, AwsImageUtils $awsImageUtils): Response
    {
        $form = $this->createForm(BookReviewType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $bookReview = $this->createReviewFromFormData($form);
            $imageFile = $form->get(BookReviewType::$review_image_name)->getData();
            if($imageFile){
                $this->handleImageData(awsImageUtils: $awsImageUtils,imageFile: $imageFile, bookReview: $bookReview);
            }
            $this->createSections($request, $bookReview);
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('home');
        }
        return $this->renderForm('book_review/create_review.twig',[
            'form' => $form
        ]);
    }


    private function handleImageData( AwsImageUtils $awsImageUtils,
                                      UploadedFile $imageFile,
                                      BookReview $bookReview){
            $image  = $awsImageUtils->uploadImageToBucketeer($imageFile);
            $bookReview->setFrontImage($image);
    }
    //todo
    //maybe refactor some of the logic here
    private function createReviewFromFormData(FormInterface $form):BookReview{
        $user = $this->getUser();
        $rating = new Rating();
        $review = new BookReview();
        $review->setRating($rating);
        $review->setCreator($user);
        $review->setBook($form->getData()['book']);
        $review->setCreationDate(new \DateTime());
        $review->setTitle($form->getData()['title']);
        return $review;
    }

    private function createSections(Request $request, BookReview $bookReview){

        $requestBag = $request->request;
        $numberOfSections = $requestBag->get('book_review')['number_sections'];
        for($sectionNumber= 1; $sectionNumber <= $numberOfSections; $sectionNumber ++){
            $section = new ReviewSection();
            $section->setBookReview($bookReview);
            $sectionTitle = $requestBag->get('section_' . $sectionNumber ."_title");
            $sectionSummary = $requestBag->get('section_' . $sectionNumber ."_summary");
            $section->setHeading($sectionTitle);
            $section->setText($sectionSummary);
            $this->getManager()->persist($section);
        }

    }


    #[Route('/books/{id}', name: 'edit_book_review')]
    public function editBookReview(Request $request, BookReview $bookReview):Response{
        if($bookReview -> getCreator() !==$this->getUser()){
            $this->redirectToRoute('home');
        }
        $form = $this->createBookReviewForm($bookReview);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $bookReview = $form->getData();
            $bookReview->setCreator($this->getUser());
            $this->persistAndFlush($bookReview);
            return $this->redirectToRoute('home');
        }
        return $this->renderForm('book_review/book_review_edit.html.twig',[
            'form' => $form
        ]);
    }

}
