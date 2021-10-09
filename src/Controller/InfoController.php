<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{


    #[Route('/apropos', name: 'apropos')]
    public function apropos(): Response
    {
        return $this->render('info/apropos.html.twig', [
            'controller_name' => 'InfoController',
        ]);
    }

 
 
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('info/cgu.html.twig', [
            'controller_name' => 'InfoController',
        ]);
    }
}

    

