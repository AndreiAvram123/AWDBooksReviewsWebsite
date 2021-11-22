<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;

class BaseController extends AbstractController
{
    protected function isFormButtonClicked(FormInterface $form, string $buttonName): bool
    {
        /** @var SubmitButton $button */
        $button = $form->get($buttonName);
        return $button->isClicked();
    }
    protected function canAccessFormData(FormInterface $form) : bool{
        return $form-> isSubmitted() && $form->isValid();
    }



    protected function getManager(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }
    protected function persistAndFlush($object){
        $this->getManager()->persist($object);
        $this->getManager()->flush();
    }

}