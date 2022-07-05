<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * une fonction de controller appelle une action, pour rappel name = "nomducontroller_nomedel'action"
     * 
     * @Route("/", name="default_home", methods={"GET"})
     */
   public function home(){
    return $this->render('default/home.html.twig');
   }
}