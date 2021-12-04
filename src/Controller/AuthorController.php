<?php

namespace App\Controller;

use App\Entity\BookAuthor;
use App\Form\AuthorType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends BaseController
{

   #[Route('/authors/create', name: 'create_author_path')]
   public function createAuthor():Response{
       $createAuthorForm = $this->createForm(AuthorType::class);
       //when form data is available use the callback
       if($this->canAccessFormData($createAuthorForm)){
           $author = $createAuthorForm->getData();
           $this->persistAndFlush($author);
           return $this->redirectToRoute('home');
       }

       return $this->renderForm('author/create_author.html.twig',[
               'createAuthorForm' =>$createAuthorForm
           ]
       );
   }
}