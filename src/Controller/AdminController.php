<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends BaseController
{

    #[Route("/admin/dashboard",name: 'dashboard', methods: ["GET"])]
    public function dashboard():Response{

      return $this->render('admin/dashboard.html.twig');
    }
}