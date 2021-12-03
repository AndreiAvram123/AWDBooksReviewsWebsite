<?php

namespace App\Controller;

use App\Entity\BookReview;
use App\Entity\User;
use App\Repository\UserRepository;
use App\utils\aws\AwsImageUtils;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;

class BaseController extends AbstractController
{
    public function __construct(private RequestStack $requestStack)
   {

   }

    protected function isFormButtonClicked(FormInterface $form, string $buttonName): bool
    {
        /** @var SubmitButton $button */
        $button = $form->get($buttonName);
        return $button->isClicked();
    }
    protected function canAccessFormData(FormInterface $form) : bool{
        return $form-> isSubmitted() && $form->isValid();
    }

    protected function createForm(string $type, $data = null, array $options = []): FormInterface{
        $form = parent::createForm($type,$data,$options);
        $form ->handleRequest($this->requestStack->getCurrentRequest());
        return $form;
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