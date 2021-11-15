<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifyUserRoleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends BaseController
{

    #[Route("/admin/dashboard",name: 'dashboard', methods: ["GET"])]
    public function dashboard():Response{

        $users = $this->getManager()->getRepository(User::class)->findAll();
        $modifyUserRoleForm = $this->createForm(ModifyUserRoleType::class);
      return $this->renderForm('admin/dashboard.html.twig',
      [
          'users' => $users,
          'modifyUserRoleForm' =>$modifyUserRoleForm
      ]);
    }



}