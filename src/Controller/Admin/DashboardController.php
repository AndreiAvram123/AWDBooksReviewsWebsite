<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The dashboard controller takes most of its functionality from the AbstractDashboardController
 * which is provided by the easy admin bundle
 */
class DashboardController extends AbstractDashboardController
{


    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $totalUsers = $this->getDoctrine()
            ->getRepository(User::class)
            ->count(array());
        $totalPublicBooks = $this->getDoctrine()
            ->getRepository(Book::class)
            ->countPubliclyAvailable();

        $totalPublicReviews = $this->getDoctrine()
            ->getRepository(BookReview::class)
            ->countPubliclyAvailable();

       return $this->render('admin/dashboard.html.twig',
        [
            "totalUsers" => $totalUsers,
            "totalPublicReviews"=>$totalPublicReviews,
            "totalPublicBooks" => $totalPublicBooks
        ]
       );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle("<a href='/'>Home</a>")
            ;
    }

    public function configureMenuItems(): iterable
    {
          yield  MenuItem::section('Sections');
          yield  MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
          yield  MenuItem::linkToCrud('Users','fa fa-user',User::class );
    }
}
