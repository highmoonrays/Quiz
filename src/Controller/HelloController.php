<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /**
     * @Route("/", name="hello_page")
     */
    public function hello()
    {
        return $this->render('hello/hello.html.twig');
    }
}
