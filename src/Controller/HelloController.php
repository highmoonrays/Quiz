<?php
declare(strict_types=1);
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
        if ($this->getUser()) {
            return $this->redirectToRoute('main_page');
        }
        return $this->render('hello/hello.html.twig');
    }
}
